<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Call your custom UserSeeder only
        $this->call(UserSeeder::class);
        $this->call(TaxSeeder::class);
    }
}
