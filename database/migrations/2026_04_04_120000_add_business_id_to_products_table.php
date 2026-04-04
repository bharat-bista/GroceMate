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
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('business_id')
                ->nullable()
                ->after('id')
                ->constrained('businesses')
                ->nullOnDelete();

            $table->index(['business_id', 'name', 'brand_id'], 'products_business_name_brand_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('products_business_name_brand_index');
            $table->dropConstrainedForeignId('business_id');
        });
    }
};
