<?php

namespace Database\Seeders;

use App\Models\InterfaceColorVariation;
use Database\Seeders\Catalog\RBasicInterfaceColorItems;
use Illuminate\Database\Seeder;

/**
 * R BASIC pantone tişört tablosundan üretilen PNG’ler için Renk Varyasyonları kayıtları.
 *
 * PNG betiği: `scripts/split_rbasic_tshirt_pantone_sheet.py` (kaynak: `scripts/assets/tshirt-pantone-sheet.png`)
 *
 * Bu veriler ayrıca migration `2026_05_07_211000_seed_r_basic_interface_color_variations` ile de yüklenir.
 *
 * Çalıştırma (Laravel kökü `src`):
 *   php artisan db:seed --class=RBasicTshirtInterfaceColorVariationsSeeder
 *
 * Docker (depo kökü, `docker-compose.yml` ile):
 *   docker compose exec b2b_app php artisan db:seed --class=RBasicTshirtInterfaceColorVariationsSeeder
 */
class RBasicTshirtInterfaceColorVariationsSeeder extends Seeder
{
    public function run(): void
    {
        foreach (RBasicInterfaceColorItems::records() as $row) {
            InterfaceColorVariation::query()->updateOrCreate(
                ['name' => $row['name']],
                [
                    'image_path' => $row['image_path'],
                    'sort_order' => $row['sort_order'],
                    'is_active' => true,
                ]
            );
        }
    }
}
