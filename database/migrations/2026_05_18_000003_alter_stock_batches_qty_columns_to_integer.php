<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Round any legacy decimal values (e.g. 3.001 → 3) before narrowing the column type.
        DB::statement('UPDATE stock_batches SET qty_received = ROUND(qty_received), qty_remaining = ROUND(qty_remaining)');

        DB::statement('ALTER TABLE stock_batches MODIFY qty_received INT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE stock_batches MODIFY qty_remaining INT UNSIGNED NOT NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE stock_batches MODIFY qty_received DECIMAL(12,3) NOT NULL');
        DB::statement('ALTER TABLE stock_batches MODIFY qty_remaining DECIMAL(12,3) NOT NULL');
    }
};
