<?php

namespace Database\Seeders;

use App\Models\TaxClass;
use App\Models\TaxRate;
use Illuminate\Database\Seeder;

class TaxSeeder extends Seeder
{
    public function run(): void
    {
        $class = TaxClass::firstOrCreate(
            ['title' => 'Vergiye tabi ürünler'],
            ['sort_order' => 0]
        );

        TaxRate::firstOrCreate(
            [
                'tax_class_id' => $class->id,
                'name' => 'KDV %18',
            ],
            [
                'rate' => 18,
                'type' => TaxRate::TYPE_PERCENTAGE,
                'geo_zone' => 'Türkiye',
                'sort_order' => 0,
                'is_active' => true,
            ]
        );
    }
}
