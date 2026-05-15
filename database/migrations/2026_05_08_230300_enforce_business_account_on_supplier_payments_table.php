<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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

        DB::table('supplier_payments')
            ->whereNull('business_account')
            ->update(['business_account' => $defaultBusinessId]);

        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        Schema::table('supplier_payments', function (Blueprint $table) {
            $table->dropForeign(['business_account']);
        });

        DB::statement('ALTER TABLE supplier_payments MODIFY business_account BIGINT UNSIGNED NOT NULL');

        Schema::table('supplier_payments', function (Blueprint $table) {
            $table->foreign('business_account')
                ->references('id')
                ->on('businesses')
                ->restrictOnDelete();
            $table->index('payment_reference', 'supplier_payments_payment_reference_index');
        });
    }

    public function down(): void
    {
        Schema::table('supplier_payments', function (Blueprint $table) {
            $table->dropForeign(['business_account']);
            $table->dropIndex('supplier_payments_payment_reference_index');
        });

        DB::statement('ALTER TABLE supplier_payments MODIFY business_account BIGINT UNSIGNED NULL');

        Schema::table('supplier_payments', function (Blueprint $table) {
            $table->foreign('business_account')
                ->references('id')
                ->on('businesses')
                ->nullOnDelete();
        });
    }
};
