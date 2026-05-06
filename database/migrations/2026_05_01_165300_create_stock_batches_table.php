<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('purchase_item_id')->constrained()->onDelete('cascade');
            $table->string('batch_no', 100);
            $table->decimal('qty_received', 12, 3);
            $table->decimal('qty_remaining', 12, 3);
            $table->decimal('unit_cost', 12, 2);
            $table->date('expiry_date')->nullable();
            $table->date('purchased_on');
            $table->enum('status', ['active', 'depleted', 'expired'])->default('active');
            $table->timestamps();
            $table->index(['product_id', 'status', 'purchased_on']);
            $table->index('batch_no');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_batches');
    }
};
