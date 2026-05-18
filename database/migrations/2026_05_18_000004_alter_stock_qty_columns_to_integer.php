<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Round any legacy decimal values before narrowing the column type.
        DB::statement('UPDATE stock SET quantity = ROUND(quantity), reorder_level = ROUND(reorder_level)');

        DB::statement('ALTER TABLE stock MODIFY quantity INT NOT NULL DEFAULT 0');
        DB::statement('ALTER TABLE stock MODIFY reorder_level INT NOT NULL DEFAULT 0');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE stock MODIFY quantity DECIMAL(12,3) NOT NULL DEFAULT 0.000');
        DB::statement('ALTER TABLE stock MODIFY reorder_level DECIMAL(12,3) NOT NULL DEFAULT 0.000');
    }
};
