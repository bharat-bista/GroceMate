<?php
// database/migrations/xxxx_create_purchase_items_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('purchase_items', function (Blueprint $table) {
      $table->id();
      $table->foreignId('purchase_id')->constrained('purchases')->cascadeOnUpdate()->cascadeOnDelete();
      $table->foreignId('product_id')->constrained('products')->cascadeOnUpdate()->restrictOnDelete();
      $table->decimal('qty', 12, 3);
      $table->decimal('unit_cost', 12, 2);
      $table->date('expiry_date')->nullable();
      $table->decimal('line_total', 12, 2)->default(0);
      $table->timestamps();

      $table->index(['product_id','expiry_date']);
    });
  }

  public function down(): void {
    Schema::dropIfExists('purchase_items');
  }
};
