<?php

use App\Models\InterfaceFabricTypeVariation;
use Illuminate\Database\Migrations\Migration;

/**
 * Yönetim paneli → Kumaş türü varyasyonları için Compact Penye preset satırları.
 * Görsel yüklemeden ürün ön seçim listesinde çıkmaz; kayıtlar panelde düzenlenebilir.
 */
return new class extends Migration
{
    public function up(): void
    {
        $items = [
            ['name' => '30/1 Compact Penye Süprem — 155-160 gr/m²', 'sort_order' => 10],
            ['name' => '16/1 Compact Penye Süprem — 230 gr/m²', 'sort_order' => 20],
            ['name' => '24/1 Compact Penye Süprem — 175-185 gr/m²', 'sort_order' => 30],
            ['name' => '18/1 Compact Penye Süprem — 200-210 gr/m²', 'sort_order' => 40],
            ['name' => '30/2 Compact Penye Süprem — 190-195 gr/m²', 'sort_order' => 50],
            ['name' => '36/1 Compact Penye Full Lyc Süprem — 160-165 gr/m²', 'sort_order' => 60],
            ['name' => '28/1 Compact Penye Lyc Süprem — 200-210 gr/m²', 'sort_order' => 70],
        ];

        foreach ($items as $item) {
            InterfaceFabricTypeVariation::firstOrCreate(
                ['name' => $item['name']],
                [
                    'image_path' => null,
                    'sort_order' => $item['sort_order'],
                    'is_active' => true,
                ]
            );
        }
    }

    public function down(): void
    {
        $names = [
            '30/1 Compact Penye Süprem — 155-160 gr/m²',
            '16/1 Compact Penye Süprem — 230 gr/m²',
            '24/1 Compact Penye Süprem — 175-185 gr/m²',
            '18/1 Compact Penye Süprem — 200-210 gr/m²',
            '30/2 Compact Penye Süprem — 190-195 gr/m²',
            '36/1 Compact Penye Full Lyc Süprem — 160-165 gr/m²',
            '28/1 Compact Penye Lyc Süprem — 200-210 gr/m²',
        ];

        InterfaceFabricTypeVariation::query()->whereIn('name', $names)->delete();
    }
};
