<?php

use App\Models\ProductCustomizationRow;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('product_customization_rows')) {
            return;
        }

        $rows = [
            ['position_name' => '1-SOL ÖN GÖĞÜS', 'default_width' => 12, 'default_height' => 8, 'default_color_count' => 3, 'default_print_technique_slug' => 'emprime', 'sort_order' => 10],
            ['position_name' => '2-SAĞ ÖN GÖĞÜS', 'default_width' => 12, 'default_height' => 8, 'default_color_count' => 3, 'default_print_technique_slug' => 'emprime', 'sort_order' => 20],
            ['position_name' => '3-ÖN GÖĞÜS', 'default_width' => 18, 'default_height' => 12, 'default_color_count' => 3, 'default_print_technique_slug' => 'emprime', 'sort_order' => 30],
            ['position_name' => '4-SAĞ KOL (pazu hizası)', 'default_width' => 8, 'default_height' => 12, 'default_color_count' => 2, 'default_print_technique_slug' => 'embroidery', 'sort_order' => 40],
            ['position_name' => '5-SOL KOL (pazu hizası)', 'default_width' => 8, 'default_height' => 12, 'default_color_count' => 2, 'default_print_technique_slug' => 'embroidery', 'sort_order' => 50],
            ['position_name' => '6-SAĞ KOL (bilek hizası)', 'default_width' => 8, 'default_height' => 6, 'default_color_count' => 2, 'default_print_technique_slug' => 'embroidery', 'sort_order' => 60],
            ['position_name' => '7-SOL KOL (bilek hizası)', 'default_width' => 8, 'default_height' => 6, 'default_color_count' => 2, 'default_print_technique_slug' => 'embroidery', 'sort_order' => 70],
            ['position_name' => '8-ÖN ALT ETEK ÜSTÜ - sağ', 'default_width' => 10, 'default_height' => 10, 'default_color_count' => 2, 'default_print_technique_slug' => 'emprime', 'sort_order' => 80],
            ['position_name' => '9-ÖN ALT ETEK ÜSTÜ - sol', 'default_width' => 10, 'default_height' => 10, 'default_color_count' => 2, 'default_print_technique_slug' => 'emprime', 'sort_order' => 90],
            ['position_name' => '10-ARKA ENSE', 'default_width' => 10, 'default_height' => 4, 'default_color_count' => 1, 'default_print_technique_slug' => 'embroidery', 'sort_order' => 100],
            ['position_name' => '11-ARKA SIRT', 'default_width' => 28, 'default_height' => 35, 'default_color_count' => 4, 'default_print_technique_slug' => 'direct_digital', 'sort_order' => 110],
            ['position_name' => '12-ARKA ALT ETEK ÜSTÜ - sağ', 'default_width' => 10, 'default_height' => 10, 'default_color_count' => 2, 'default_print_technique_slug' => 'emprime', 'sort_order' => 120],
            ['position_name' => '13-ARKA ALT ETEK ÜSTÜ - sol', 'default_width' => 10, 'default_height' => 10, 'default_color_count' => 2, 'default_print_technique_slug' => 'emprime', 'sort_order' => 130],
            ['position_name' => '15-KAPŞON ÜSTÜ - öne', 'default_width' => 12, 'default_height' => 12, 'default_color_count' => 3, 'default_print_technique_slug' => 'direct_digital', 'sort_order' => 150],
            ['position_name' => '16-KAPŞON ÜSTÜ - arka', 'default_width' => 12, 'default_height' => 12, 'default_color_count' => 3, 'default_print_technique_slug' => 'direct_digital', 'sort_order' => 160],
            ['position_name' => '17-ALLOVER (tüm yüzey baskı)', 'default_width' => 30, 'default_height' => 40, 'default_color_count' => 4, 'default_print_technique_slug' => 'direct_digital', 'sort_order' => 170],
            ['position_name' => '18-ALLOVER (tüm yüzey baskı)', 'default_width' => 30, 'default_height' => 40, 'default_color_count' => 4, 'default_print_technique_slug' => 'direct_digital', 'sort_order' => 180],
        ];

        $positionNames = array_column($rows, 'position_name');

        ProductCustomizationRow::query()
            ->whereNotIn('position_name', $positionNames)
            ->update(['is_active' => false]);

        foreach ($rows as $item) {
            ProductCustomizationRow::query()->updateOrCreate(
                ['position_name' => $item['position_name']],
                array_merge($item, ['is_active' => true])
            );
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('product_customization_rows')) {
            return;
        }

        $positionNames = [
            '1-SOL ÖN GÖĞÜS',
            '2-SAĞ ÖN GÖĞÜS',
            '3-ÖN GÖĞÜS',
            '4-SAĞ KOL (pazu hizası)',
            '5-SOL KOL (pazu hizası)',
            '6-SAĞ KOL (bilek hizası)',
            '7-SOL KOL (bilek hizası)',
            '8-ÖN ALT ETEK ÜSTÜ - sağ',
            '9-ÖN ALT ETEK ÜSTÜ - sol',
            '10-ARKA ENSE',
            '11-ARKA SIRT',
            '12-ARKA ALT ETEK ÜSTÜ - sağ',
            '13-ARKA ALT ETEK ÜSTÜ - sol',
            '15-KAPŞON ÜSTÜ - öne',
            '16-KAPŞON ÜSTÜ - arka',
            '17-ALLOVER (tüm yüzey baskı)',
            '18-ALLOVER (tüm yüzey baskı)',
        ];

        ProductCustomizationRow::query()
            ->whereIn('position_name', $positionNames)
            ->delete();
    }
};
