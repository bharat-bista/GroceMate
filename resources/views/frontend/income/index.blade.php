@extends('inventory.layouts.inventory')

@section('title', 'Ecommerce Income')
@section('heading', 'Ecommerce Income')
@section('subtitle', 'Income generated from ecommerce orders by business account')

@section('content')
<div class="space-y-6">
    @if(session('success'))
        <div class="p-4 rounded-xl bg-green-100 text-green-700 border border-green-200 shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 rounded-xl bg-red-100 text-red-700 border border-red-200 shadow-sm">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-2xl p-6 text-white border-2 border-black">
            <p class="text-blue-100 text-sm font-medium">Total Ecommerce Income</p>
            <p class="text-3xl font-bold mt-2">Rs {{ number_format((float) $totalIncome, 2) }}</p>
        </div>

        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-2xl p-6 text-white border-2 border-black">
            <p class="text-green-100 text-sm font-medium">This Month</p>
            <p class="text-3xl font-bold mt-2">Rs {{ number_format((float) $thisMonthIncome, 2) }}</p>
        </div>

        <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-2xl p-6 text-white border-2 border-black">
            <p class="text-purple-100 text-sm font-medium">Today</p>
            <p class="text-3xl font-bold mt-2">Rs {{ number_format((float) $todayIncome, 2) }}</p>
        </div>

        <div class="bg-gradient-to-r from-amber-500 to-amber-600 rounded-2xl p-6 text-white border-2 border-black">
            <p class="text-amber-100 text-sm font-medium">Estimated Profit</p>
            <p class="text-3xl font-bold mt-2">Rs {{ number_format((float) $estimatedProfit, 2) }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-slate-800 mb-4">Business Income (This Month)</h3>
            <div class="space-y-4">
                @forelse($businessIncomeStats as $business)
                    <div class="flex items-center justify-between p-4 bg-slate-50 rounded-xl">
                        <div>
                            <p class="font-semibold text-slate-800">{{ $business->business_name }}</p>
                            <p class="text-sm text-slate-500">{{ $business->business_type }} • {{ $business->orders_count }} paid orders</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-slate-600">Income: <span class="font-bold text-emerald-600">Rs {{ number_format((float) $business->total_income, 2) }}</span></p>
                            <p class="text-xs text-slate-500">Est. Profit: Rs {{ number_format((float) $business->estimated_profit, 2) }}</p>
                            <p class="text-xs text-slate-500">Current Balance: Rs {{ number_format((float) $business->current_balance, 2) }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-slate-500 text-center py-4">No business income data available.</p>
                @endforelse
            </div>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-slate-800 mb-4">Payment Methods</h3>
            <div class="space-y-3">
                @forelse($paymentStats as $payment)
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-slate-700">{{ strtoupper($payment->payment_method) }}</span>
                        <div class="text-right">
                            <p class="text-sm font-semibold text-slate-800">Rs {{ number_format((float) $payment->total, 2) }}</p>
                            <p class="text-xs text-slate-500">{{ $payment->count }} orders</p>
                        </div>
                    </div>
                @empty
                    <p class="text-slate-500 text-center py-4">No payment data.</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="bg-white shadow-xl rounded-3xl border border-slate-200 overflow-hidden">
        <div class="p-6 border-b border-slate-200">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-slate-900">Ecommerce Income Transactions</h2>
                    <p class="text-sm text-slate-600 mt-1">Paid and non-cancelled ecommerce order income split by business account.</p>
                </div>
                <div class="relative inline-block text-left">
                    <button type="button" id="incomeExportDropdown" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">
                        Export All
                    </button>
                    <div id="incomeExportMenu" class="hidden origin-top-right absolute right-0 mt-2 w-64 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50 border border-gray-200">
                        <div class="py-2">
                            <div class="px-4 py-2 border-b border-gray-200">
                                <div class="space-y-2">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">From Date</label>
                                        <input type="date" id="exportFromDate" class="w-full text-xs border border-gray-300 rounded px-2 py-1">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">To Date</label>
                                        <input type="date" id="exportToDate" class="w-full text-xs border border-gray-300 rounded px-2 py-1">
                                    </div>
                                </div>
                            </div>
                            <div class="py-1">
                                <button onclick="exportEcommerceIncome('pdf')" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Export as PDF</button>
                                <button onclick="exportEcommerceIncome('excel')" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Export as Excel</button>
                                <button onclick="exportEcommerceIncome('csv')" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Export as CSV</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="p-6 bg-slate-50 border-b border-slate-200">
            <form method="GET" action="{{ route('inventory.ecommerce-income.index') }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Search</label>
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Order #, customer, phone, business..."
                               class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Business</label>
                        <select name="business_id" class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                            <option value="">All Businesses</option>
                            @foreach($businesses as $business)
                                <option value="{{ $business->id }}" @selected((string) request('business_id') === (string) $business->id)>{{ $business->business_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Payment Method</label>
                        <select name="payment_method" class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                            <option value="">All Methods</option>
                            <option value="esewa" @selected(request('payment_method') === 'esewa')>eSewa</option>
                            <option value="connectips" @selected(request('payment_method') === 'connectips')>Connect IPS</option>
                            <option value="cod" @selected(request('payment_method') === 'cod')>COD</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">From Date</label>
                        <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">To Date</label>
                        <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                    </div>
                </div>
                <div class="flex gap-3">
                    <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 text-sm font-medium">Search</button>
                    <a href="{{ route('inventory.ecommerce-income.index') }}" class="px-4 py-2 bg-slate-600 text-white rounded-lg hover:bg-slate-700 text-sm font-medium">Clear</a>
                </div>
            </form>
        </div>

        @if($incomes->count() > 0)
            <div class="text-xs text-slate-500 px-6 pt-4 pb-2">
                Showing {{ $incomes->firstItem() }} to {{ $incomes->lastItem() }} of {{ $incomes->total() }} results
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-100 border-b border-slate-200">
                    <tr>
                        <th class="text-left px-6 py-4 text-xs font-medium text-slate-700 uppercase tracking-wider">#</th>
                        <th class="text-left px-6 py-4 text-xs font-medium text-slate-700 uppercase tracking-wider">Order #</th>
                        <th class="text-left px-6 py-4 text-xs font-medium text-slate-700 uppercase tracking-wider">Date</th>
                        <th class="text-left px-6 py-4 text-xs font-medium text-slate-700 uppercase tracking-wider">Customer</th>
                        <th class="text-left px-6 py-4 text-xs font-medium text-slate-700 uppercase tracking-wider">Business</th>
                        <th class="text-left px-6 py-4 text-xs font-medium text-slate-700 uppercase tracking-wider">Units</th>
                        <th class="text-left px-6 py-4 text-xs font-medium text-slate-700 uppercase tracking-wider">Income</th>
                        <th class="text-left px-6 py-4 text-xs font-medium text-slate-700 uppercase tracking-wider">Est. Profit</th>
                        <th class="text-left px-6 py-4 text-xs font-medium text-slate-700 uppercase tracking-wider">Method</th>
                        <th class="text-left px-6 py-4 text-xs font-medium text-slate-700 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-200">
                    @forelse($incomes as $index => $income)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4 text-sm text-slate-500">{{ ($incomes->currentPage() - 1) * $incomes->perPage() + $index + 1 }}</td>
                            <td class="px-6 py-4 text-sm font-medium text-slate-900">{{ $income->order_number }}</td>
                            <td class="px-6 py-4 text-sm text-slate-900">{{ \Carbon\Carbon::parse($income->order_created_at)->format('M d, Y') }}</td>
                            <td class="px-6 py-4 text-sm text-slate-900">
                                <div>{{ $income->customer_name }}</div>
                                <div class="text-xs text-slate-500">{{ $income->customer_phone }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-900">{{ $income->business_name ?? 'Unassigned' }}</td>
                            <td class="px-6 py-4 text-sm text-slate-900">{{ (int) $income->total_units }}</td>
                            <td class="px-6 py-4 text-sm font-semibold text-emerald-600">Rs {{ number_format((float) $income->business_gross_income, 2) }}</td>
                            <td class="px-6 py-4 text-sm text-amber-700">Rs {{ number_format((float) $income->estimated_profit, 2) }}</td>
                            <td class="px-6 py-4 text-sm text-slate-900 uppercase">{{ $income->payment_method }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <a href="{{ route('inventory.orders.show', $income->order_id) }}" class="text-emerald-600 hover:text-emerald-900 font-medium">View Order</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-6 py-12 text-center text-slate-500">No ecommerce income records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($incomes->hasPages())
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-200">
                {{ $incomes->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>

<script>
const incomeExportDropdown = document.getElementById('incomeExportDropdown');
const incomeExportMenu = document.getElementById('incomeExportMenu');

if (incomeExportDropdown && incomeExportMenu) {
    incomeExportDropdown.addEventListener('click', function () {
        incomeExportMenu.classList.toggle('hidden');
    });
}

function exportEcommerceIncome(type) {
    const fromDate = document.getElementById('exportFromDate').value;
    const toDate = document.getElementById('exportToDate').value;

    const params = new URLSearchParams(window.location.search);
    params.delete('page');

    if (fromDate) {
        params.set('from', fromDate);
    }

    if (toDate) {
        params.set('to', toDate);
    }

    const baseUrl = "{{ route('inventory.ecommerce-income.export', ['type' => 'TYPE']) }}".replace('TYPE', type);
    const query = params.toString();
    window.location.href = query ? `${baseUrl}?${query}` : baseUrl;
}

document.addEventListener('click', function (event) {
    if (incomeExportDropdown && incomeExportMenu && !incomeExportDropdown.contains(event.target) && !incomeExportMenu.contains(event.target)) {
        incomeExportMenu.classList.add('hidden');
    }
});
</script>
@endsection
