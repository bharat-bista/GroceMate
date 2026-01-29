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
    Schema::table('purchase_items', function (Blueprint $table) {
        $table->string('product_name')->after('product_id');
        $table->string('unit')->after('product_name');
    });
}

public function down(): void
{
    Schema::table('purchase_items', function (Blueprint $table) {
        $table->dropColumn(['product_name', 'unit']);
    });
}
};
