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
    Schema::create('customers', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('phone')->nullable();
    $table->string('email')->nullable();
    $table->enum('customer_type',['retail','wholesale','regular'])->default('retail');
    
    $table->decimal('opening_due', 15, 2)->default(0); // starting debt
    $table->decimal('total_due', 15, 2)->default(0);   // current debt
    
    $table->text('address')->nullable();
    $table->text('notes')->nullable();
    $table->timestamps();
});

}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
