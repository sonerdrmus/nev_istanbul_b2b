<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Ürün bazlı «Model Seçiniz» (image/select) varyasyonlarını Model Yönetimi kütüphanesine taşır.
 *
 * - Her seçenek bir preset olur (ad + görsel + çarpan aynıysa mevcut preset yeniden kullanılır), ürüne atanır.
 * - Mevcut seçenek satırları korunur (üst seçenek bağlantıları bozulmasın); yalnızca preset FK'si yazılır.
 * - Ürün başına tek varyasyon kalır: aynı üründeki diğer Model* adımlarının seçenekleri ilkine taşınır, kardeşler silinir.
 * - Kalıp* adlı varyasyonlara dokunulmaz.
 */
return new class extends Migration
{
    public function up(): void
    {
        foreach (['product_variations', 'product_variation_options', 'interface_product_model_variations', 'interface_product_model_variation_product'] as $table) {
            if (! Schema::hasTable($table)) {
                return;
            }
        }
        if (! Schema::hasColumn('product_variation_options', 'interface_product_model_variation_id')) {
            return;
        }

        $candidates = DB::table('product_variations')
            ->whereIn('type', ['image', 'select'])
            ->orderBy('product_id')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->filter(fn ($row): bool => $this->isModelVariationName((string) $row->name))
            ->groupBy('product_id');

        $presetsCreated = 0;
        $variationsConverted = 0;
        $variationsMerged = 0;
        $optionsLinked = 0;

        /** @var array<string, int> name|image|price => preset id */
        $presetCache = [];
        $now = now();

        foreach ($candidates as $productId => $variations) {
            DB::transaction(function () use ($productId, $variations, &$presetCache, &$presetsCreated, &$variationsConverted, &$variationsMerged, &$optionsLinked, $now): void {
                $keep = $variations->first();
                $usedInProduct = [];

                foreach ($variations as $variation) {
                    $options = DB::table('product_variation_options')
                        ->where('product_variation_id', $variation->id)
                        ->orderBy('sort_order')
                        ->orderBy('id')
                        ->get();

                    foreach ($options as $option) {
                        $name = trim((string) $option->option_value);
                        if ($name === '') {
                            continue;
                        }
                        $image = filled($option->option_image) ? (string) $option->option_image : null;
                        $multiplier = (float) $option->price_delta > 0 ? round((float) $option->price_delta, 3) : 1.0;
                        $key = mb_strtolower($name).'|'.($image ?? '').'|'.number_format($multiplier, 3, '.', '');

                        $presetId = null;
                        if (! isset($usedInProduct[$key])) {
                            $presetId = $presetCache[$key] ?? null;
                        }

                        if ($presetId === null) {
                            $presetId = (int) DB::table('interface_product_model_variations')->insertGetId([
                                'name' => $name,
                                'name_en' => filled($option->option_value_en ?? null) ? $option->option_value_en : null,
                                'name_it' => filled($option->option_value_it ?? null) ? $option->option_value_it : null,
                                'image_path' => $image,
                                'price_multiplier' => $multiplier,
                                'sort_order' => (int) $option->sort_order,
                                'is_active' => true,
                                'created_at' => $now,
                                'updated_at' => $now,
                            ]);
                            $presetsCreated++;
                            if (! isset($usedInProduct[$key])) {
                                $presetCache[$key] = $presetId;
                            }
                        }
                        $usedInProduct[$key] = true;

                        DB::table('interface_product_model_variation_product')->insertOrIgnore([
                            'interface_product_model_variation_id' => $presetId,
                            'product_id' => (int) $productId,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ]);

                        $update = ['interface_product_model_variation_id' => $presetId];
                        if ((int) $variation->id !== (int) $keep->id) {
                            $update['product_variation_id'] = $keep->id;
                        }
                        DB::table('product_variation_options')->where('id', $option->id)->update($update);
                        $optionsLinked++;
                    }

                    if ((int) $variation->id !== (int) $keep->id) {
                        // Bu adıma bağlı diğer varyasyonlar kalan adıma bağlansın.
                        DB::table('product_variations')
                            ->where('product_id', $productId)
                            ->where('depends_on', $variation->name)
                            ->update(['depends_on' => $keep->name]);
                        DB::table('product_variations')->where('id', $variation->id)->delete();
                        $variationsMerged++;
                    }
                }

                DB::table('product_variations')->where('id', $keep->id)->update([
                    'type' => 'model_type',
                    'updated_at' => $now,
                ]);
                $variationsConverted++;
            });
        }

        $message = "Model library migration: presets created={$presetsCreated}, variations converted={$variationsConverted}, sibling variations merged/deleted={$variationsMerged}, options linked={$optionsLinked}";
        echo $message.PHP_EOL;
        \Illuminate\Support\Facades\Log::info($message);
    }

    public function down(): void
    {
        // Veri geri alınamaz: model_type varyasyonları image tipine döndürülür, kütüphane kayıtları tablo silinince gider.
        if (Schema::hasTable('product_variations')) {
            DB::table('product_variations')->where('type', 'model_type')->update(['type' => 'image']);
        }
    }

    private function isModelVariationName(string $name): bool
    {
        $name = trim($name);

        if (! preg_match('/model/iu', $name)) {
            return false;
        }

        // Kalıp / Kalip / fit adımlarına dokunma.
        return ! preg_match('/kal[ıiİI]p|fit/iu', $name);
    }
};
