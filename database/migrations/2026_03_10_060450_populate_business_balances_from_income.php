<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Business;
use App\Models\POS\Income;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (
            !Schema::hasTable('businesses')
            || !Schema::hasColumn('businesses', 'balance')
            || !Schema::hasTable('incomes')
            || !Schema::hasColumn('incomes', 'business_id')
        ) {
            return;
        }

        // Get total income for each business
        $businessIncomes = Income::selectRaw('business_id, SUM(amount_received) as total_income')
            ->whereNotNull('business_id')
            ->groupBy('business_id')
            ->get();

        // Update each business balance
        foreach ($businessIncomes as $income) {
            Business::where('id', $income->business_id)
                ->update(['balance' => $income->total_income]);
        }

        // Also update businesses that have no income records to 0
        $businessesWithNoIncome = Business::whereNotIn('id', 
            Income::whereNotNull('business_id')->pluck('business_id')
        )->get();

        foreach ($businessesWithNoIncome as $business) {
            $business->update(['balance' => 0]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reset all business balances to 0
        Business::query()->update(['balance' => 0]);
    }
};
