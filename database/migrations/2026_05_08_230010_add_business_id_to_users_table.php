<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('business_id')
                ->nullable()
                ->after('id')
                ->constrained('businesses')
                ->restrictOnDelete();
        });

        $defaultBusinessId = DB::table('businesses')->min('id');
        if (!$defaultBusinessId) {
            $defaultBusinessId = DB::table('businesses')->insertGetId([
                'business_name' => 'Default Business',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        DB::table('users')
            ->whereNull('business_id')
            ->update(['business_id' => $defaultBusinessId]);

        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        DB::statement('ALTER TABLE users MODIFY business_id BIGINT UNSIGNED NOT NULL');
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['business_id']);
            $table->dropColumn('business_id');
        });
    }
};
