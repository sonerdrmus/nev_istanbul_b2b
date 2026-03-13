<?php

namespace Database\Seeders;

use App\Models\SizeTable;
use App\Models\SizeTableColumn;
use Illuminate\Database\Seeder;

class SizeTableSeeder extends Seeder
{
    public function run(): void
    {
        $tables = [
            [
                'name' => 'Erkek',
                'slug' => 'erkek',
                'title' => 'BEDEN TABLOSU (ERKEK)',
                'trigger_variation_name' => 'Cinsiyet',
                'trigger_option_value' => 'Erkek',
                'sort_order' => 0,
                'columns' => ['XS', 'S', 'M', 'L', 'XL', '2XL', '3XL', '4XL', '5XL', '6XL', '7XL', '8XL'],
            ],
            [
                'name' => 'Kadın',
                'slug' => 'kadin',
                'title' => 'Kadın Beden Seçiniz',
                'trigger_variation_name' => 'Cinsiyet',
                'trigger_option_value' => 'Kadın',
                'sort_order' => 1,
                'columns' => ['XS', 'S', 'M', 'L', 'XL', '2XL', '3XL', '4XL', '5XL', '6XL', '7XL', '8XL'],
            ],
            [
                'name' => 'Çocuk',
                'slug' => 'cocuk',
                'title' => 'BEDEN - ÇOCUK',
                'trigger_variation_name' => 'Cinsiyet',
                'trigger_option_value' => 'Çocuk',
                'sort_order' => 2,
                'columns' => ['98', '104', '110', '116', '122', '128', '134', '140', '152', '158', '164'],
            ],
        ];

        foreach ($tables as $index => $data) {
            $columns = $data['columns'];
            unset($data['columns']);
            $table = SizeTable::updateOrCreate(
                ['slug' => $data['slug']],
                $data
            );
            $table->columns()->delete();
            foreach ($columns as $i => $sizeValue) {
                $table->columns()->create([
                    'size_value' => $sizeValue,
                    'sort_order' => $i,
                ]);
            }
        }
    }
}
