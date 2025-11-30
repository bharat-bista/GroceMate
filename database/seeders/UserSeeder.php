<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        

DB::table('users')->insert([
  'full_name' => 'Bharat Bista',
  'gender'    => 'male',
  'email'     => 'bharatbista2062@gmail.com',
  'password'  => Hash::make('password'),
  'role_id'   => 1,
  'status'    => 'Y',
]);

        //
    }
}
