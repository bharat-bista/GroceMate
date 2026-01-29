<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('purchase_items', function (Blueprint $table) {
        $table->decimal('base_cost', 12, 2)->default(0)->after('unit_cost');
        $table->decimal('tax_total', 12, 2)->default(0)->after('base_cost');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_items', function (Blueprint $table) {
        $table->dropColumn(['base_cost', 'tax_total']);
    });
    }

};
