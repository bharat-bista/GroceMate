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
        Schema::table('supplier_payments', function (Blueprint $table) {
            $table->string('payment_type')->after('amount')->nullable(); // external or integrated
            $table->string('payment_method_external')->after('payment_method')->nullable(); // cash, bank, esewa_external, khalti_external
            $table->string('payment_method_integrated')->after('payment_method_external')->nullable(); // esewa_integrated
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('supplier_payments', function (Blueprint $table) {
            $table->dropColumn(['payment_type', 'payment_method_external', 'payment_method_integrated']);
        });
    }
};
