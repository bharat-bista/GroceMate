<?php

namespace Database\Seeders;

use App\Models\Tax;
use Illuminate\Database\Seeder;

class TaxSeeder extends Seeder
{
    public function run()
    {
        $taxes = [
            [
                'name' => 'VAT',
                'type' => 'percentage',
                'rate' => 13.00,
            ],
            [
                'name' => 'Excise Duty',
                'type' => 'fixed',
                'rate' => 5.00,
            ],
            [
                'name' => 'Liquor Tax',
                'type' => 'fixed',
                'rate' => 10.00,
            ],
        ];

        foreach ($taxes as $tax) {
            Tax::create($tax);
        }
    }
}