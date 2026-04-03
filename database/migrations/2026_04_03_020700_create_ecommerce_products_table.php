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
        Schema::create('ecommerce_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            
            // Basic Information
            $table->string('sku')->nullable()->unique();
            $table->enum('status', ['in_stock', 'out_of_stock', 'coming_soon'])->default('in_stock');
            
            // Pricing
            $table->decimal('previous_price', 10, 2)->nullable();
            $table->decimal('mrp', 10, 2)->default(0);
            $table->decimal('discount_percent', 5, 2)->default(0);
            $table->decimal('display_price', 10, 2)->default(0);
            $table->decimal('profit', 10, 2)->default(0);
            
            // SEO Settings
            $table->string('meta_keywords')->nullable();
            
            // Product Description (rich text)
            $table->longText('description')->nullable();
            
            // Thumbnail Image
            $table->string('thumbnail')->nullable();
            
            $table->timestamps();

            $table->index('product_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ecommerce_products');
    }
};
