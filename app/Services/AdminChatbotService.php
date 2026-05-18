<?php

namespace App\Services;

use App\Models\Business;
use App\Models\POS\Customer;
use App\Models\POS\Income;
use App\Models\POS\Invoice;
use App\Models\POS\InvoiceItem;
use App\Models\PurchaseItem;
use App\Models\Purchase;
use App\Models\Stock;
use App\Models\Supplier;
use App\Models\SupplierPayment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AdminChatbotService
{
    /**
     * Generate an answer for the admin panel chatbot.
     *
     * Strategy:
     * 1. Handle a few safe, high-confidence questions locally in Laravel.
     * 2. Forward everything else to the Python Vanna sidecar when enabled.
     * 3. Return a helpful fallback message when the sidecar is not configured.
     */
    public function answer(string $message, ?User $user = null): array
    {
        $message = trim($message);
        $normalizedMessage = Str::of($message)->lower()->squish()->value();

        if ($message === '') {
            return $this->buildResponse(
                answer: 'Please type a question so I can help.',
                source: 'validation',
            );
        }

        if ($localAnswer = $this->tryGreeting($normalizedMessage)) {
            return $localAnswer;
        }

        if ($localAnswer = $this->tryCalculator($message, $normalizedMessage)) {
            return $localAnswer;
        }

        if ($localAnswer = $this->tryLowStockAnswer($normalizedMessage)) {
            return $localAnswer;
        }

        if ($localAnswer = $this->trySupplierCountAnswer($normalizedMessage)) {
            return $localAnswer;
        }

        if ($localAnswer = $this->tryExpiryAnswer($normalizedMessage)) {
            return $localAnswer;
        }

        if ($localAnswer = $this->tryCustomerDueAnswer($normalizedMessage)) {
            return $localAnswer;
        }

        if ($localAnswer = $this->trySupplierDueAnswer($normalizedMessage)) {
            return $localAnswer;
        }

        if ($localAnswer = $this->tryProductDemandAnswer($normalizedMessage)) {
            return $localAnswer;
        }

        if ($localAnswer = $this->tryTopSalesBusinessAnswer($normalizedMessage)) {
            return $localAnswer;
        }

        if ($localAnswer = $this->tryBusinessFinancialSnapshotAnswer($normalizedMessage)) {
            return $localAnswer;
        }

        if ($localAnswer = $this->tryBusinessReportAnswer($normalizedMessage)) {
            return $localAnswer;
        }

        if ($vannaAnswer = $this->tryVannaSidecar($message, $user)) {
            return $vannaAnswer;
        }

        return $this->buildResponse(
            answer: "I can only answer questions related to system data (stock, sales, purchases, customers, suppliers) or mathematical calculations. Please try rephrasing your question.",
            source: 'fallback',
            meta: [
                'vanna_enabled' => config('chatbot.vanna.enabled'),
            ],
        );
    }

    /**
     * Return a friendly local answer for common greeting-style prompts.
     */
    protected function tryGreeting(string $normalizedMessage): ?array
    {
        $greetings = ['hello', 'hi', 'hey', 'good morning', 'good afternoon', 'good evening'];

        foreach ($greetings as $greeting) {
            if ($normalizedMessage === $greeting) {
                return $this->buildResponse(
                    answer: "Hello! Ask me about low stock, expiry alerts, supplier counts, or calculations. Once the Vanna service is configured, I can handle broader database questions too.",
                    source: 'laravel-rule',
                );
            }
        }

        return null;
    }

    /**
     * Answer questions that clearly look like calculator requests.
     *
     * A calculator is better handled deterministically than by asking the LLM
     * to estimate math from plain text.
     */
    protected function tryCalculator(string $message, string $normalizedMessage): ?array
    {
        if (! $this->looksLikeCalculation($normalizedMessage)) {
            return null;
        }

        try {
            $calculation = $this->evaluateCalculationPrompt($message);
        } catch (\Throwable $exception) {
            return $this->buildResponse(
                answer: 'I recognized that as a calculation request, but I could not parse the expression safely. Try a format like `2450 + 18%`, `120 * 12`, or `(5000 - 1200) / 2`.',
                source: 'calculator',
                meta: [
                    'error' => $exception->getMessage(),
                ],
            );
        }

        return $this->buildResponse(
            answer: "Result: {$calculation['formatted_result']}\nExplanation: {$calculation['explanation']}",
            source: 'calculator',
            meta: [
                'expression' => $calculation['expression'],
                'raw_result' => $calculation['result'],
            ],
        );
    }

    /**
     * Handle low-stock questions using the same stock rule the dashboard uses.
     *
     * This keeps the chatbot aligned with the existing business logic in
     * App\Models\Stock::scopeLowStock().
     */
    protected function tryLowStockAnswer(string $normalizedMessage): ?array
    {
        if (! Str::contains($normalizedMessage, ['low stock', 'out of stock', 'reorder'])) {
            return null;
        }

        $rows = Stock::query()
            ->with(['product.business'])
            ->lowStock()
            ->whereHas('product')
            ->orderBy('quantity')
            ->limit(config('chatbot.limits.low_stock_rows', 10))
            ->get();

        $count = Stock::query()
            ->lowStock()
            ->whereHas('product')
            ->count();

        if ($count === 0) {
            return $this->buildResponse(
                answer: 'Good news: I could not find any low-stock products right now.',
                source: 'laravel-rule',
                meta: ['count' => 0],
            );
        }

        $lines = $rows->map(function (Stock $stock): string {
            $productName = $stock->product->name ?? 'Unknown product';
            $businessName = $stock->product->business->business_name ?? 'No business';

            return sprintf(
                '- %s (%s): qty %s, reorder level %s',
                $productName,
                $businessName,
                $this->formatDecimal($stock->quantity),
                $this->formatDecimal($stock->reorder_level),
            );
        })->implode("\n");

        $answer = "I found {$count} low-stock product(s). Here are the first {$rows->count()}:\n{$lines}";

        return $this->buildResponse(
            answer: $answer,
            source: 'laravel-rule',
            meta: [
                'count' => $count,
                'rows_previewed' => $rows->count(),
            ],
        );
    }

    /**
     * Answer simple supplier-count questions locally.
     */
    protected function trySupplierCountAnswer(string $normalizedMessage): ?array
    {
        if (! Str::contains($normalizedMessage, ['how many suppliers', 'supplier count', 'total suppliers'])) {
            return null;
        }

        $count = Supplier::query()->count();

        return $this->buildResponse(
            answer: "There are currently {$count} supplier record(s) in GroceMate.",
            source: 'laravel-rule',
            meta: ['count' => $count],
        );
    }

    /**
     * Answer expiry-related questions locally with a small preview list.
     */
    protected function tryExpiryAnswer(string $normalizedMessage): ?array
    {
        if (! Str::contains($normalizedMessage, ['expiry', 'expiring', 'expired'])) {
            return null;
        }

        $today = Carbon::today();
        $days = $this->extractDaysWindow($normalizedMessage) ?? 30;
        $limit = config('chatbot.limits.expiry_rows', 10);

        if (Str::contains($normalizedMessage, 'expired')) {
            $items = PurchaseItem::query()
                ->with(['purchase.business', 'purchase.supplier'])
                ->whereNotNull('expiry_date')
                ->where('expiry_date', '<', $today)
                ->orderByDesc('expiry_date')
                ->limit($limit)
                ->get();

            $count = PurchaseItem::query()
                ->whereNotNull('expiry_date')
                ->where('expiry_date', '<', $today)
                ->count();

            return $this->buildExpiryResponse(
                heading: "I found {$count} expired purchase item(s).",
                items: $items,
                count: $count,
                source: 'laravel-rule',
            );
        }

        $endDate = $today->copy()->addDays($days);

        $items = PurchaseItem::query()
            ->with(['purchase.business', 'purchase.supplier'])
            ->whereNotNull('expiry_date')
            ->whereBetween('expiry_date', [$today, $endDate])
            ->orderBy('expiry_date')
            ->limit($limit)
            ->get();

        $count = PurchaseItem::query()
            ->whereNotNull('expiry_date')
            ->whereBetween('expiry_date', [$today, $endDate])
            ->count();

        return $this->buildExpiryResponse(
            heading: "I found {$count} purchase item(s) expiring within {$days} day(s).",
            items: $items,
            count: $count,
            source: 'laravel-rule',
        );
    }

    /**
     * Return a formatted expiry preview response.
     */
    protected function buildExpiryResponse(string $heading, Collection $items, int $count, string $source): array
    {
        if ($count === 0) {
            return $this->buildResponse(
                answer: $heading,
                source: $source,
                meta: ['count' => 0],
            );
        }

        $lines = $items->map(function (PurchaseItem $item): string {
            $businessName = $item->purchase->business->business_name ?? 'No business';
            $supplierName = $item->purchase->supplier->name ?? 'No supplier';
            $expiryDate = $item->expiry_date?->format('Y-m-d') ?? 'Unknown date';

            return sprintf(
                '- %s | Expiry: %s | Supplier: %s | Business: %s',
                $item->product_name,
                $expiryDate,
                $supplierName,
                $businessName,
            );
        })->implode("\n");

        return $this->buildResponse(
            answer: "{$heading}\nHere are the first {$items->count()}:\n{$lines}",
            source: $source,
            meta: [
                'count' => $count,
                'rows_previewed' => $items->count(),
            ],
        );
    }

    /**
     * Answer customer outstanding due questions.
     */
    protected function tryCustomerDueAnswer(string $normalizedMessage): ?array
    {
        if (! Str::contains($normalizedMessage, ['customer due', 'customer dues', 'customer outstanding', 'receivable', 'collect from customer'])) {
            return null;
        }

        $limit = (int) config('chatbot.limits.customer_due_rows', 10);

        $rows = Customer::query()
            ->where('total_due', '>', 0)
            ->orderByDesc('total_due')
            ->limit($limit)
            ->get(['id', 'name', 'phone', 'total_due']);

        $count = Customer::query()->where('total_due', '>', 0)->count();
        $totalDue = (float) Customer::query()->where('total_due', '>', 0)->sum('total_due');

        if ($count === 0) {
            return $this->buildResponse(
                answer: 'There are currently no customers with outstanding due.',
                source: 'laravel-rule',
                meta: ['count' => 0, 'total_due' => 0],
            );
        }

        $lines = $rows->map(function (Customer $customer): string {
            $phone = trim((string) ($customer->phone ?? ''));
            $phoneText = $phone !== '' ? " ({$phone})" : '';

            return sprintf(
                '- %s%s: Rs %s due',
                $customer->name,
                $phoneText,
                $this->formatDecimal($customer->total_due),
            );
        })->implode("\n");

        return $this->buildResponse(
            answer: "Total customer receivable is Rs {$this->formatDecimal($totalDue)} across {$count} customer(s).\nHere are the top {$rows->count()}:\n{$lines}",
            source: 'laravel-rule',
            meta: [
                'count' => $count,
                'total_due' => $totalDue,
                'rows_previewed' => $rows->count(),
            ],
        );
    }

    /**
     * Answer supplier outstanding due questions.
     */
    protected function trySupplierDueAnswer(string $normalizedMessage): ?array
    {
        if (! Str::contains($normalizedMessage, ['supplier due', 'supplier dues', 'supplier outstanding', 'payable', 'due to supplier', 'pay suppliers'])) {
            return null;
        }

        $limit = (int) config('chatbot.limits.supplier_due_rows', 10);

        $rows = Supplier::query()
            ->where('total_due', '>', 0)
            ->orderByDesc('total_due')
            ->limit($limit)
            ->get(['id', 'name', 'phone', 'total_due']);

        $count = Supplier::query()->where('total_due', '>', 0)->count();
        $totalDue = (float) Supplier::query()->where('total_due', '>', 0)->sum('total_due');

        if ($count === 0) {
            return $this->buildResponse(
                answer: 'There are currently no suppliers with outstanding due.',
                source: 'laravel-rule',
                meta: ['count' => 0, 'total_due' => 0],
            );
        }

        $lines = $rows->map(function (Supplier $supplier): string {
            $phone = trim((string) ($supplier->phone ?? ''));
            $phoneText = $phone !== '' ? " ({$phone})" : '';

            return sprintf(
                '- %s%s: Rs %s due',
                $supplier->name,
                $phoneText,
                $this->formatDecimal($supplier->total_due),
            );
        })->implode("\n");

        return $this->buildResponse(
            answer: "Total supplier payable is Rs {$this->formatDecimal($totalDue)} across {$count} supplier(s).\nHere are the top {$rows->count()}:\n{$lines}",
            source: 'laravel-rule',
            meta: [
                'count' => $count,
                'total_due' => $totalDue,
                'rows_previewed' => $rows->count(),
            ],
        );
    }

    /**
     * Answer high-demand / low-demand product questions from invoice history.
     */
    protected function tryProductDemandAnswer(string $normalizedMessage): ?array
    {
        if (! Str::contains($normalizedMessage, ['demand', 'high demand', 'low demand', 'fast moving', 'slow moving', 'most sold', 'least sold'])) {
            return null;
        }

        $days = $this->extractDaysWindow($normalizedMessage) ?? 30;
        $limit = (int) config('chatbot.limits.demand_rows', 10);
        $isLowDemand = Str::contains($normalizedMessage, ['low demand', 'least sold', 'slow moving']);
        $isAllTime = Str::contains($normalizedMessage, ['all time', 'overall']);

        $query = InvoiceItem::query()
            ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
            ->where('invoices.cancellation_status', 'active')
            ->leftJoin('products', 'invoice_items.product_id', '=', 'products.id')
            ->leftJoin('businesses', 'invoices.business_id', '=', 'businesses.id')
            ->selectRaw('COALESCE(products.name, invoice_items.product_name) as product_name')
            ->selectRaw('COALESCE(businesses.business_name, "No business") as business_name')
            ->selectRaw('SUM(invoice_items.qty) as total_units')
            ->groupBy(
                DB::raw('COALESCE(products.name, invoice_items.product_name)'),
                DB::raw('COALESCE(businesses.business_name, "No business")')
            )
            ->havingRaw('SUM(invoice_items.qty) > 0');

        if (! $isAllTime) {
            $query->whereDate('invoices.invoice_date', '>=', Carbon::today()->subDays($days));
        }

        $rows = $isLowDemand
            ? $query->orderBy('total_units')->limit($limit)->get()
            : $query->orderByDesc('total_units')->limit($limit)->get();

        if ($rows->isEmpty()) {
            $scopeText = $isAllTime ? 'all time' : "the last {$days} day(s)";

            return $this->buildResponse(
                answer: "I could not find product demand records for {$scopeText}.",
                source: 'laravel-rule',
                meta: ['count' => 0],
            );
        }

        $lines = $rows->map(function ($row): string {
            return sprintf(
                '- %s (%s): %s unit(s)',
                (string) $row->product_name,
                (string) $row->business_name,
                $this->formatDecimal($row->total_units),
            );
        })->implode("\n");

        $scopeText = $isAllTime ? 'all-time data' : "the last {$days} day(s)";
        $demandLabel = $isLowDemand ? 'low-demand' : 'high-demand';

        return $this->buildResponse(
            answer: "Here are the top {$rows->count()} {$demandLabel} products based on {$scopeText}:\n{$lines}",
            source: 'laravel-rule',
            meta: [
                'rows_previewed' => $rows->count(),
                'days' => $isAllTime ? null : $days,
                'mode' => $isLowDemand ? 'low' : 'high',
            ],
        );
    }

    /**
     * Answer "which business has more sale" style questions.
     */
    protected function tryTopSalesBusinessAnswer(string $normalizedMessage): ?array
    {
        if (! Str::contains($normalizedMessage, ['business have more sale', 'business has more sale', 'bussiness have more sale', 'bussiness has more sale', 'top business sale', 'highest sale business', 'business with highest sale', 'most sales business'])) {
            return null;
        }

        $days = $this->extractDaysWindow($normalizedMessage);
        $limit = (int) config('chatbot.limits.business_rows', 5);

        $query = Invoice::query()
            ->where('invoices.cancellation_status', 'active')
            ->join('businesses', 'invoices.business_id', '=', 'businesses.id')
            ->selectRaw('businesses.id as business_id')
            ->selectRaw('businesses.business_name as business_name')
            ->selectRaw('SUM(invoices.total_cost) as total_sales')
            ->selectRaw('COUNT(invoices.id) as invoice_count')
            ->groupBy('businesses.id', 'businesses.business_name')
            ->orderByDesc('total_sales');

        if ($days) {
            $query->whereDate('invoices.invoice_date', '>=', Carbon::today()->subDays($days));
        }

        $rows = $query->limit($limit)->get();

        if ($rows->isEmpty()) {
            return $this->buildResponse(
                answer: 'I could not find sales records tied to businesses.',
                source: 'laravel-rule',
                meta: ['count' => 0],
            );
        }

        $top = $rows->first();
        $lines = $rows->map(function ($row): string {
            return sprintf(
                '- %s: Rs %s from %s invoice(s)',
                (string) $row->business_name,
                $this->formatDecimal($row->total_sales),
                $this->formatDecimal($row->invoice_count),
            );
        })->implode("\n");

        $scope = $days ? "in the last {$days} day(s)" : 'for all-time data';

        return $this->buildResponse(
            answer: "Top sales business {$scope} is {$top->business_name} (Rs {$this->formatDecimal($top->total_sales)}).\nTop {$rows->count()} businesses:\n{$lines}",
            source: 'laravel-rule',
            meta: [
                'rows_previewed' => $rows->count(),
                'top_business_id' => $top->business_id,
                'days' => $days,
            ],
        );
    }

    /**
     * Answer overall payable/receivable/collection/payment snapshot questions.
     */
    protected function tryBusinessFinancialSnapshotAnswer(string $normalizedMessage): ?array
    {
        if (! Str::contains($normalizedMessage, ['purchase due', 'due to pay', 'collection', 'payable', 'receivable', 'business data', 'bussiness data', 'pay collection'])) {
            return null;
        }

        $totalSales = (float) Invoice::query()->where('cancellation_status', 'active')->sum('total_cost');
        $totalPurchases = (float) Purchase::query()->sum('total_cost');
        $customerReceivable = (float) Customer::query()->sum('total_due');
        $supplierPayable = (float) Supplier::query()->sum('total_due');
        $dueCollection = (float) Income::query()
            ->where('income_type', 'Due Collection')
            ->where('amount_received', '>', 0)
            ->sum('amount_received');
        $supplierPayment = (float) SupplierPayment::query()->sum('amount');
        $grossIncomeReceived = (float) Income::query()->where('amount_received', '>', 0)->sum('amount_received');
        $netCashFlow = $grossIncomeReceived - $supplierPayment;

        $answer = sprintf(
            "Business finance snapshot:\n- Total sales: Rs %s\n- Total purchases: Rs %s\n- Customer receivable (to collect): Rs %s\n- Supplier payable (to pay): Rs %s\n- Due collection received: Rs %s\n- Supplier payment made: Rs %s\n- Net cash flow (income - supplier payment): Rs %s",
            $this->formatDecimal($totalSales),
            $this->formatDecimal($totalPurchases),
            $this->formatDecimal($customerReceivable),
            $this->formatDecimal($supplierPayable),
            $this->formatDecimal($dueCollection),
            $this->formatDecimal($supplierPayment),
            $this->formatDecimal($netCashFlow),
        );

        return $this->buildResponse(
            answer: $answer,
            source: 'laravel-rule',
            meta: [
                'total_sales' => $totalSales,
                'total_purchases' => $totalPurchases,
                'customer_receivable' => $customerReceivable,
                'supplier_payable' => $supplierPayable,
                'due_collection' => $dueCollection,
                'supplier_payment' => $supplierPayment,
                'net_cash_flow' => $netCashFlow,
            ],
        );
    }

    /**
     * Return a compact business report when the prompt asks for a report.
     */
    protected function tryBusinessReportAnswer(string $normalizedMessage): ?array
    {
        if (! Str::contains($normalizedMessage, ['business report', 'bussiness report', 'report about business', 'report about bussiness', 'business summary', 'report of business'])) {
            return null;
        }

        $business = $this->matchBusinessFromMessage($normalizedMessage);
        $days = $this->extractDaysWindow($normalizedMessage);

        if ($business) {
            $salesQuery = $business->invoices()->where('cancellation_status', 'active');
            $purchaseQuery = $business->purchases();
            $incomeQuery = $business->incomes()->where('amount_received', '>', 0);
            $paymentQuery = $business->supplierPayments();

            if ($days) {
                $startDate = Carbon::today()->subDays($days);
                $salesQuery->whereDate('invoice_date', '>=', $startDate);
                $purchaseQuery->whereDate('purchase_date', '>=', $startDate);
                $incomeQuery->whereDate('transaction_date', '>=', $startDate);
                $paymentQuery->whereDate('date', '>=', $startDate);
            }

            $salesTotal = (float) $salesQuery->sum('total_cost');
            $purchaseTotal = (float) $purchaseQuery->sum('total_cost');
            $incomeTotal = (float) $incomeQuery->sum('amount_received');
            $dueCollectionTotal = (float) (clone $incomeQuery)->where('income_type', 'Due Collection')->sum('amount_received');
            $supplierPaymentTotal = (float) $paymentQuery->sum('amount');
            $grossProfitLoss = $salesTotal - $purchaseTotal;
            $netCashFlow = $incomeTotal - $supplierPaymentTotal;

            $scope = $days ? "last {$days} day(s)" : 'all-time';

            return $this->buildResponse(
                answer: sprintf(
                    "Business report for %s (%s):\n- Sales: Rs %s\n- Purchases: Rs %s\n- Income received: Rs %s\n- Due collections: Rs %s\n- Supplier payments: Rs %s\n- Gross profit/loss: Rs %s\n- Net cash flow: Rs %s\n- Current balance: Rs %s",
                    $business->business_name,
                    $scope,
                    $this->formatDecimal($salesTotal),
                    $this->formatDecimal($purchaseTotal),
                    $this->formatDecimal($incomeTotal),
                    $this->formatDecimal($dueCollectionTotal),
                    $this->formatDecimal($supplierPaymentTotal),
                    $this->formatDecimal($grossProfitLoss),
                    $this->formatDecimal($netCashFlow),
                    $this->formatDecimal($business->balance),
                ),
                source: 'laravel-rule',
                meta: [
                    'business_id' => $business->id,
                    'days' => $days,
                ],
            );
        }

        $rows = Invoice::query()
            ->where('invoices.cancellation_status', 'active')
            ->join('businesses', 'invoices.business_id', '=', 'businesses.id')
            ->selectRaw('businesses.business_name as business_name')
            ->selectRaw('SUM(invoices.total_cost) as total_sales')
            ->groupBy('businesses.id', 'businesses.business_name')
            ->orderByDesc('total_sales')
            ->limit((int) config('chatbot.limits.business_rows', 5))
            ->get();

        $lines = $rows->map(function ($row): string {
            return sprintf(
                '- %s: Rs %s',
                (string) $row->business_name,
                $this->formatDecimal($row->total_sales),
            );
        })->implode("\n");

        return $this->buildResponse(
            answer: "I could not match a specific business name in your report request, so here are the top businesses by sales:\n{$lines}",
            source: 'laravel-rule',
            meta: ['rows_previewed' => $rows->count()],
        );
    }

    /**
     * Try to resolve a business name from the message.
     */
    protected function matchBusinessFromMessage(string $normalizedMessage): ?Business
    {
        return Business::query()
            ->get(['id', 'business_name', 'balance'])
            ->first(function (Business $business) use ($normalizedMessage) {
                return Str::contains($normalizedMessage, Str::of($business->business_name)->lower()->squish()->value());
            });
    }

    /**
     * Forward broader questions to the Python Vanna sidecar.
     *
     * The sidecar is intentionally optional. If it is disabled or offline, the
     * rest of the admin panel still works and the widget stays usable.
     */
    protected function tryVannaSidecar(string $message, ?User $user): ?array
    {
        if (! config('chatbot.vanna.enabled')) {
            return null;
        }

        $baseUrl = rtrim((string) config('chatbot.vanna.base_url'), '/');
        $endpoint = '/' . ltrim((string) config('chatbot.vanna.chat_endpoint'), '/');

        try {
            $response = Http::timeout((int) config('chatbot.vanna.timeout_seconds', 20))
                ->acceptJson()
                ->post($baseUrl . $endpoint, [
                    'message' => $message,
                    'user' => [
                        'id' => $user?->id,
                        'email' => $user?->email,
                        'name' => $user?->full_name,
                        'groups' => $user?->isAdmin() ? ['admin'] : ['user'],
                    ],
                ]);
        } catch (\Throwable $exception) {
            Log::warning('Admin chatbot could not reach the Vanna sidecar.', [
                'error' => $exception->getMessage(),
                'url' => $baseUrl . $endpoint,
            ]);

            return $this->buildResponse(
                answer: 'The Vanna chatbot service is enabled in config, but Laravel could not reach it. Please start the Python sidecar and check its URL.',
                source: 'vanna-unavailable',
                meta: [
                    'service_url' => $baseUrl . $endpoint,
                ],
            );
        }

        if (! $response->successful()) {
            Log::warning('Admin chatbot received an error from the Vanna sidecar.', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return $this->buildResponse(
                answer: 'The Vanna chatbot service responded with an error. Please review the Python service logs.',
                source: 'vanna-error',
                meta: [
                    'status' => $response->status(),
                ],
            );
        }

        $payload = $response->json();

        return $this->buildResponse(
            answer: (string) ($payload['answer'] ?? 'The Vanna sidecar returned an empty response.'),
            source: 'vanna-service',
            meta: [
                'service_payload' => $payload['meta'] ?? null,
            ],
        );
    }

    /**
     * Build a consistent response payload for the widget.
     */
    protected function buildResponse(string $answer, string $source, array $meta = []): array
    {
        return [
            'answer' => $answer,
            'source' => $source,
            'meta' => $meta,
        ];
    }

    /**
     * Decide whether a prompt looks like a math request.
     */
    protected function looksLikeCalculation(string $normalizedMessage): bool
    {
        return Str::contains($normalizedMessage, ['calculate', 'calculator', 'solve', 'plus', 'minus', 'multiply', 'divide', '%'])
            || (bool) preg_match('/[\d\)\(]\s*[\+\-\*\/]\s*[\d\(\)]/', $normalizedMessage);
    }

    /**
     * Evaluate a natural-language calculation prompt.
     *
     * The method supports:
     * - standard arithmetic like 120 * 12
     * - bracketed arithmetic like (5000 - 1200) / 2
     * - percentage add/subtract like 2450 + 18%
     */
    protected function evaluateCalculationPrompt(string $message): array
    {
        $normalizedExpression = $this->normalizeMathExpression($message);

        // Handle "base +/- percent%" as a special case because people often
        // ask VAT-style questions in that form.
        if (preg_match('/^\s*(-?\d+(?:\.\d+)?)\s*([\+\-])\s*(\d+(?:\.\d+)?)%\s*$/', $normalizedExpression, $matches)) {
            $base = (float) $matches[1];
            $operator = $matches[2];
            $percent = (float) $matches[3];
            $percentValue = $base * ($percent / 100);
            $result = $operator === '+' ? $base + $percentValue : $base - $percentValue;

            return [
                'expression' => $normalizedExpression,
                'result' => $result,
                'formatted_result' => $this->formatDecimal($result),
                'explanation' => sprintf(
                    '%s %s %s%% = %s',
                    $this->formatDecimal($base),
                    $operator,
                    $this->formatDecimal($percent),
                    $this->formatDecimal($result),
                ),
            ];
        }

        $tokens = $this->tokenizeExpression($normalizedExpression);
        $state = ['index' => 0];
        $result = $this->parseExpression($tokens, $state);

        if (($tokens[$state['index']] ?? null) !== null) {
            throw new \RuntimeException('Unexpected token while parsing the expression.');
        }

        return [
            'expression' => $normalizedExpression,
            'result' => $result,
            'formatted_result' => $this->formatDecimal($result),
            'explanation' => "{$normalizedExpression} = {$this->formatDecimal($result)}",
        ];
    }

    /**
     * Translate a human-friendly prompt into a cleaner math expression.
     */
    protected function normalizeMathExpression(string $message): string
    {
        $expression = Str::of($message)->lower()->value();

        $replacements = [
            'what is ' => '',
            'how much is ' => '',
            'calculate ' => '',
            'calculator ' => '',
            'solve ' => '',
            'equals' => '',
            'equal to' => '',
            '?' => '',
            'vat' => '',
            'plus' => '+',
            'minus' => '-',
            'multiplied by' => '*',
            'multiply by' => '*',
            'multiply' => '*',
            'times' => '*',
            'divided by' => '/',
            'divide by' => '/',
            'divide' => '/',
        ];

        $expression = str_replace(array_keys($replacements), array_values($replacements), $expression);
        $expression = preg_replace('/\s+/', ' ', $expression ?? '');
        $expression = preg_replace('/[^0-9\.\+\-\*\/\(\)% ]/', '', $expression ?? '');

        return trim((string) $expression);
    }

    /**
     * Tokenize the expression into numbers, operators, and brackets.
     */
    protected function tokenizeExpression(string $expression): array
    {
        if ($expression === '') {
            throw new \RuntimeException('The expression is empty.');
        }

        preg_match_all('/\d+(?:\.\d+)?|[\+\-\*\/\(\)]/', $expression, $matches);
        $tokens = $matches[0] ?? [];

        if ($tokens === []) {
            throw new \RuntimeException('No valid calculation tokens were found.');
        }

        return $tokens;
    }

    /**
     * Parse addition and subtraction.
     */
    protected function parseExpression(array $tokens, array &$state): float
    {
        $value = $this->parseTerm($tokens, $state);

        while (in_array($tokens[$state['index']] ?? null, ['+', '-'], true)) {
            $operator = $tokens[$state['index']];
            $state['index']++;
            $right = $this->parseTerm($tokens, $state);

            $value = $operator === '+' ? $value + $right : $value - $right;
        }

        return $value;
    }

    /**
     * Parse multiplication and division.
     */
    protected function parseTerm(array $tokens, array &$state): float
    {
        $value = $this->parseFactor($tokens, $state);

        while (in_array($tokens[$state['index']] ?? null, ['*', '/'], true)) {
            $operator = $tokens[$state['index']];
            $state['index']++;
            $right = $this->parseFactor($tokens, $state);

            if ($operator === '/' && $right == 0.0) {
                throw new \RuntimeException('Division by zero is not allowed.');
            }

            $value = $operator === '*' ? $value * $right : $value / $right;
        }

        return $value;
    }

    /**
     * Parse a number, unary minus, or a bracketed expression.
     */
    protected function parseFactor(array $tokens, array &$state): float
    {
        $current = $tokens[$state['index']] ?? null;

        if ($current === null) {
            throw new \RuntimeException('The expression ended unexpectedly.');
        }

        if ($current === '-') {
            $state['index']++;

            return -1 * $this->parseFactor($tokens, $state);
        }

        if ($current === '(') {
            $state['index']++;
            $value = $this->parseExpression($tokens, $state);

            if (($tokens[$state['index']] ?? null) !== ')') {
                throw new \RuntimeException('A closing bracket is missing.');
            }

            $state['index']++;

            return $value;
        }

        if (! is_numeric($current)) {
            throw new \RuntimeException('A number was expected.');
        }

        $state['index']++;

        return (float) $current;
    }

    /**
     * Extract a day window such as "30 days" from a prompt.
     */
    protected function extractDaysWindow(string $normalizedMessage): ?int
    {
        if (preg_match('/(\d+)\s*day/', $normalizedMessage, $matches)) {
            return max(1, (int) $matches[1]);
        }

        return null;
    }

    /**
     * Format decimal numbers consistently for chatbot replies.
     */
    protected function formatDecimal(float|int|string|null $value): string
    {
        $value = (float) $value;

        if (fmod($value, 1.0) === 0.0) {
            return number_format($value, 0);
        }

        return rtrim(rtrim(number_format($value, 3, '.', ''), '0'), '.');
    }
}
