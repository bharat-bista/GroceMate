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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id');

            $table->string('name');
      $table->string('sku')->nullable()->unique();
      $table->string('unit'); // kg / liter / pcs
      $table->decimal('selling_price', 10, 2)->default(0);
      $table->text('description')->nullable();
      $table->string('image_url')->nullable();
      $table->boolean('is_active')->default(true);
      $table->boolean('is_listed')->default(false); // show in ecommerce
            $table->timestamps();
            
            $table->index(['category_id', 'is_active', 'is_listed']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
