<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sliders', function (Blueprint $table) {
            $table->string('slider_type', 20)->default('hero')->after('secondary_button_link');
            $table->unsignedTinyInteger('promo_slot')->nullable()->after('slider_type');
        });
    }

    public function down(): void
    {
        Schema::table('sliders', function (Blueprint $table) {
            $table->dropColumn(['slider_type', 'promo_slot']);
        });
    }
};
