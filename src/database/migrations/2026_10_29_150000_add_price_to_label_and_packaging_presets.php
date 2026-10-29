<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Etiket türleri ve ambalaj tercihleri için doğrudan fiyat (adet başına TL).
 * Fiyat kütüphanede tutulur, tüm ürünlerde aynıdır ve seçildiğinde birim fiyata eklenir.
 *
 * Etiket türlerinde kısa süre kullanılan fiyat çarpanı (price_multiplier) kaldırılır;
 * bağlı etiket seçeneklerinin çarpanı nötr (×1) yapılır.
 */
return new class extends Migration
{
    private const TABLES = ['interface_label_type_variations', 'interface_packaging_preference_variations'];

    public function up(): void
    {
        foreach (self::TABLES as $table) {
            if (Schema::hasTable($table) && ! Schema::hasColumn($table, 'price')) {
                Schema::table($table, function (Blueprint $t) {
                    $t->decimal('price', 12, 2)->default(0);
                });
            }
        }

        if (Schema::hasTable('interface_label_type_variations')
            && Schema::hasColumn('interface_label_type_variations', 'price_multiplier')) {
            Schema::table('interface_label_type_variations', function (Blueprint $t) {
                $t->dropColumn('price_multiplier');
            });
        }

        if (Schema::hasColumn('product_variation_options', 'interface_label_type_variation_id')) {
            DB::table('product_variation_options')
                ->whereNotNull('interface_label_type_variation_id')
                ->update(['price_delta' => 1]);
        }
    }

    public function down(): void
    {
        foreach (self::TABLES as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'price')) {
                Schema::table($table, function (Blueprint $t) {
                    $t->dropColumn('price');
                });
            }
        }
    }
};
