<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    public function run(): void
    {
        $currencies = [
            ['code' => 'TRY', 'name' => 'Türk Lirası', 'symbol' => '₺', 'exchange_rate' => 1.0, 'decimal_places' => 2, 'is_default' => true, 'sort_order' => 1],
            ['code' => 'USD', 'name' => 'Amerikan Doları', 'symbol' => '$', 'exchange_rate' => 34.0, 'decimal_places' => 2, 'is_default' => false, 'sort_order' => 2],
            ['code' => 'EUR', 'name' => 'Euro', 'symbol' => '€', 'exchange_rate' => 37.0, 'decimal_places' => 2, 'is_default' => false, 'sort_order' => 3],
        ];

        foreach ($currencies as $data) {
            Currency::updateOrCreate(
                ['code' => $data['code']],
                array_merge($data, ['is_active' => true])
            );
        }
    }
}
