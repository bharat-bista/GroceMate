<?php
// database/migrations/xxxx_create_stock_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('stock', function (Blueprint $table) {
      $table->foreignId('product_id')->primary()->constrained('products')->cascadeOnUpdate()->cascadeOnDelete();
      $table->decimal('quantity', 12, 3)->default(0);
      $table->decimal('reorder_level', 12, 3)->default(0);
      $table->timestamps();
    });
  }
  public function down(): void { Schema::dropIfExists('stock'); }
};

