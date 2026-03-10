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
            // Drop existing business_account column if it exists as string
            $table->dropColumn('business_account');
            
            // Add new business_account column as foreign key
            $table->foreignId('business_account')->nullable()->constrained('businesses')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('supplier_payments', function (Blueprint $table) {
            // Drop foreign key constraint
            $table->dropForeign(['business_account']);
            
            // Drop the foreign key column
            $table->dropColumn('business_account');
            
            // Add back the original string column
            $table->string('business_account')->nullable();
        });
    }
};
