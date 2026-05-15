<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $defaultBusinessId = DB::table('businesses')->min('id');
        if (!$defaultBusinessId) {
            $defaultBusinessId = DB::table('businesses')->insertGetId([
                'business_name' => 'Default Business',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        DB::table('invoices')->whereNull('business_id')->update(['business_id' => $defaultBusinessId]);
        DB::table('purchases')->whereNull('business_id')->update(['business_id' => $defaultBusinessId]);
        DB::table('incomes')->whereNull('business_id')->update(['business_id' => $defaultBusinessId]);
        DB::table('expenses')->whereNull('business_id')->update(['business_id' => $defaultBusinessId]);
        DB::table('products')->whereNull('business_id')->update(['business_id' => $defaultBusinessId]);
        DB::table('suppliers')->whereNull('business_account')->update(['business_account' => $defaultBusinessId]);
    }

    public function down(): void
    {
        // No-op: backfill is intentionally irreversible.
    }
};
