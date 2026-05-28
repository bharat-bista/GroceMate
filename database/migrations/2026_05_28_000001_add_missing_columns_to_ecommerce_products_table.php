<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ecommerce_products', function (Blueprint $table) {
            if (! Schema::hasColumn('ecommerce_products', 'status')) {
                $table->string('status')->default('out_of_stock');
            }
            if (! Schema::hasColumn('ecommerce_products', 'mrp')) {
                $table->decimal('mrp', 10, 2)->nullable();
            }
            if (! Schema::hasColumn('ecommerce_products', 'display_price')) {
                $table->decimal('display_price', 10, 2)->nullable();
            }
            if (! Schema::hasColumn('ecommerce_products', 'discount_percent')) {
                $table->decimal('discount_percent', 10, 2)->default(0);
            }
            if (! Schema::hasColumn('ecommerce_products', 'previous_price')) {
                $table->decimal('previous_price', 10, 2)->nullable();
            }
            if (! Schema::hasColumn('ecommerce_products', 'profit')) {
                $table->decimal('profit', 10, 2)->nullable();
            }
            if (! Schema::hasColumn('ecommerce_products', 'meta_keywords')) {
                $table->string('meta_keywords')->nullable();
            }
            if (! Schema::hasColumn('ecommerce_products', 'thumbnail')) {
                $table->string('thumbnail')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('ecommerce_products', function (Blueprint $table) {
            $table->dropColumn(array_filter([
                Schema::hasColumn('ecommerce_products', 'status')           ? 'status'           : null,
                Schema::hasColumn('ecommerce_products', 'mrp')              ? 'mrp'              : null,
                Schema::hasColumn('ecommerce_products', 'display_price')    ? 'display_price'    : null,
                Schema::hasColumn('ecommerce_products', 'discount_percent') ? 'discount_percent' : null,
                Schema::hasColumn('ecommerce_products', 'previous_price')   ? 'previous_price'   : null,
                Schema::hasColumn('ecommerce_products', 'profit')           ? 'profit'           : null,
                Schema::hasColumn('ecommerce_products', 'meta_keywords')    ? 'meta_keywords'    : null,
                Schema::hasColumn('ecommerce_products', 'thumbnail')        ? 'thumbnail'        : null,
            ]));
        });
    }
};
