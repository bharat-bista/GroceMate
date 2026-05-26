<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE incomes MODIFY COLUMN income_type ENUM('Sale', 'Due Collection', 'Other', 'Capital Injection') NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE incomes MODIFY COLUMN income_type ENUM('Sale', 'Due Collection', 'Other') NOT NULL");
    }
};
