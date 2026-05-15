<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('business_id')
                ->nullable()
                ->after('id')
                ->constrained('businesses')
                ->restrictOnDelete();
            $table->string('idempotency_key', 100)->nullable()->unique();
        });

        $defaultBusinessId = DB::table('businesses')->min('id');
        if (!$defaultBusinessId) {
            $defaultBusinessId = DB::table('businesses')->insertGetId([
                'business_name' => 'Default Business',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        if (DB::getDriverName() !== 'sqlite') {
            DB::statement(
                'UPDATE orders o
                 JOIN (
                     SELECT oi.order_id,
                            MIN(COALESCE(p.business_id, ?)) AS min_business_id,
                            MAX(COALESCE(p.business_id, ?)) AS max_business_id
                     FROM order_items oi
                     JOIN ecommerce_products ep ON ep.id = oi.product_id
                     JOIN products p ON p.id = ep.product_id
                     GROUP BY oi.order_id
                 ) t ON t.order_id = o.id
                 SET o.business_id = CASE
                     WHEN t.min_business_id = t.max_business_id THEN t.min_business_id
                     ELSE ?
                 END
                 WHERE o.business_id IS NULL',
                [$defaultBusinessId, $defaultBusinessId, $defaultBusinessId]
            );
        }

        DB::table('orders')
            ->whereNull('business_id')
            ->update(['business_id' => $defaultBusinessId]);

        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        DB::statement('ALTER TABLE orders MODIFY business_id BIGINT UNSIGNED NOT NULL');
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropUnique(['idempotency_key']);
            $table->dropForeign(['business_id']);
            $table->dropColumn(['business_id', 'idempotency_key']);
        });
    }
};
