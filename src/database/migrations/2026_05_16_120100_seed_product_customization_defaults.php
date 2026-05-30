<?php

use App\Models\ProductCustomizationPrintTechnique;
use App\Models\ProductCustomizationRow;
use App\Models\ProductCustomizationSetting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('product_customization_settings')) {
            return;
        }

        ProductCustomizationSetting::query()->firstOrCreate([], [
            'max_color_count' => 7,
            'default_print_technique_slug' => 'emprime',
        ]);

        $techniques = [
            ['name' => 'Emprime', 'slug' => 'emprime', 'sort_order' => 10],
            ['name' => 'DTF Baskı', 'slug' => 'dtf', 'sort_order' => 20],
            ['name' => 'Direk Dijital Baskı', 'slug' => 'direct_digital', 'sort_order' => 30],
            ['name' => 'Nakış', 'slug' => 'embroidery', 'sort_order' => 40],
        ];

        foreach ($techniques as $item) {
            ProductCustomizationPrintTechnique::query()->updateOrCreate(
                ['slug' => $item['slug']],
                [
                    'name' => $item['name'],
                    'sort_order' => $item['sort_order'],
                    'is_active' => true,
                ]
            );
        }

        $rows = [
            ['position_name' => 'Sol göğüs', 'default_width' => 12, 'default_height' => 8, 'default_color_count' => 3, 'default_print_technique_slug' => 'emprime', 'sort_order' => 10],
            ['position_name' => 'Sağ göğüs', 'default_width' => 12, 'default_height' => 8, 'default_color_count' => 3, 'default_print_technique_slug' => 'emprime', 'sort_order' => 20],
            ['position_name' => 'Sağ kol', 'default_width' => 8, 'default_height' => 12, 'default_color_count' => 2, 'default_print_technique_slug' => 'embroidery', 'sort_order' => 30],
            ['position_name' => 'Sol kol', 'default_width' => 8, 'default_height' => 12, 'default_color_count' => 2, 'default_print_technique_slug' => 'embroidery', 'sort_order' => 40],
            ['position_name' => 'Orta göğüs', 'default_width' => 18, 'default_height' => 12, 'default_color_count' => 3, 'default_print_technique_slug' => 'emprime', 'sort_order' => 50],
            ['position_name' => 'Sırt', 'default_width' => 28, 'default_height' => 35, 'default_color_count' => 4, 'default_print_technique_slug' => 'direct_digital', 'sort_order' => 60],
        ];

        foreach ($rows as $item) {
            ProductCustomizationRow::query()->updateOrCreate(
                [
                    'position_name' => $item['position_name'],
                    'sort_order' => $item['sort_order'],
                ],
                array_merge($item, ['is_active' => true])
            );
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('product_customization_rows')) {
            return;
        }

        ProductCustomizationRow::query()->delete();
        ProductCustomizationPrintTechnique::query()->delete();
        ProductCustomizationSetting::query()->delete();
    }
};
