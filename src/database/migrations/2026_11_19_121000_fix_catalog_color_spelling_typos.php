<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $renames = [
            'Cappucınno' => 'Cappuccino',
            'Cappucinno' => 'Cappuccino',
            'Cappucino' => 'Cappuccino',
            'Coffe Bean' => 'Coffee Bean',
            'Coffe bean' => 'Coffee Bean',
            'Coffee bean' => 'Coffee Bean',
            'Hoodie ,Kangroo' => 'Hoodie, Kangaroo',
            'Kangroo' => 'Kangaroo',
        ];

        foreach ($renames as $from => $to) {
            if (Schema::hasTable('interface_color_variations')) {
                DB::table('interface_color_variations')->where('name', $from)->update(['name' => $to]);
                if (Schema::hasColumn('interface_color_variations', 'name_en')) {
                    DB::table('interface_color_variations')->where('name_en', $from)->update(['name_en' => $to]);
                }
            }

            if (Schema::hasTable('product_variation_options')) {
                DB::table('product_variation_options')->where('option_value', $from)->update(['option_value' => $to]);
                if (Schema::hasColumn('product_variation_options', 'option_value_en')) {
                    DB::table('product_variation_options')->where('option_value_en', $from)->update(['option_value_en' => $to]);
                }
            }
        }

        // Aynı Pantone koduna bağlı yinelenen isimleri raporlamak için dokunmuyoruz;
        // bilinen yazım hatalarını düzelttik.
    }

    public function down(): void
    {
        // Geri alma bilinçli olarak yok: yazım düzeltmesi geri alınmaz.
    }
};
