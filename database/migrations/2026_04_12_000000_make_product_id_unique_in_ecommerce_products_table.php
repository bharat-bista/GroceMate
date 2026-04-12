<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Keep only the latest ecommerce row per product before adding unique constraint.
        $duplicateProductIds = DB::table('ecommerce_products')
            ->select('product_id')
            ->groupBy('product_id')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('product_id');

        foreach ($duplicateProductIds as $productId) {
            $latestId = DB::table('ecommerce_products')
                ->where('product_id', $productId)
                ->max('id');

            DB::table('ecommerce_products')
                ->where('product_id', $productId)
                ->where('id', '!=', $latestId)
                ->delete();
        }

        Schema::table('ecommerce_products', function (Blueprint $table) {
            $table->dropIndex(['product_id']);
            $table->unique('product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ecommerce_products', function (Blueprint $table) {
            $table->dropUnique(['product_id']);
            $table->index('product_id');
        });
    }
};
