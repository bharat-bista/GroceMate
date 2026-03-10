<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Business;
use App\Models\POS\Income;
use App\Models\SupplierPayment;

class RecalculateBusinessBalances extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:recalculate-business-balances';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recalculate all business balances from income and supplier payment records';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Recalculating business balances...');

        // Get all businesses
        $businesses = Business::all();

        foreach ($businesses as $business) {
            // Calculate total income for this business
            $totalIncome = Income::where('business_id', $business->id)
                ->sum('amount_received');

            // Calculate total supplier payments for this business (negative amounts)
            $totalPayments = SupplierPayment::where('business_account', $business->id)
                ->sum('amount');

            // Net balance = Income - Payments
            $netBalance = $totalIncome - $totalPayments;

            // Update business balance
            $business->update(['balance' => $netBalance]);

            $this->info("Business: {$business->business_name}");
            $this->line("  Total Income: Rs " . number_format($totalIncome, 2));
            $this->line("  Total Payments: Rs " . number_format($totalPayments, 2));
            $this->line("  Net Balance: Rs " . number_format($netBalance, 2));
            $this->line('');
        }

        $this->info('Business balances recalculated successfully!');
    }
}
