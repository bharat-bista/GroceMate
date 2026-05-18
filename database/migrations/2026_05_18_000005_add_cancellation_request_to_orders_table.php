<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('cancellation_request_status', ['pending', 'approved', 'rejected'])->nullable()->after('notes');
            $table->text('cancellation_request_reason')->nullable()->after('cancellation_request_status');
            $table->timestamp('cancellation_requested_at')->nullable()->after('cancellation_request_reason');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['cancellation_request_status', 'cancellation_request_reason', 'cancellation_requested_at']);
        });
    }
};
