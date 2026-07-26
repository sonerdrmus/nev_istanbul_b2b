<?php

namespace App\Support;

use App\Models\Product;
use App\Models\ProductCustomizationPrintTechnique;
use App\Models\ProductCustomizationRow;
use App\Models\ProductCustomizationSetting;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

/**
 * Mağaza ürün sayfası özelleştirme tablosu için yapılandırma.
 */
final class ProductCustomizationCatalog
{
    /**
     * @return array{
     *     max_color_count: int,
     *     default_print_slug: string,
     *     print_techniques: array<string, string>,
     *     rows: Collection<int, ProductCustomizationRow|object>
     * }
     */
    public static function forStore(?Product $product = null): array
    {
        if (! Schema::hasTable('product_customization_settings')) {
            return self::fallbackFromTranslations();
        }

        $settings = ProductCustomizationSetting::instance();
        $techniques = ProductCustomizationPrintTechnique::activeOrdered();
        $productId = $product?->getKey() !== null ? (int) $product->getKey() : null;

        // Ürün formu / pivot ataması tek kaynak: yalnızca bu ürüne bağlı aktif konumlar.
        if ($productId !== null && ProductCustomizationRow::productPivotTableExists()) {
            $rows = $product->relationLoaded('customizationRows')
                ? $product->customizationRows
                    ->where('is_active', true)
                    ->sortBy([['sort_order', 'asc'], ['id', 'asc']])
                    ->values()
                : $product->customizationRows()
                    ->where('is_active', true)
                    ->orderBy('sort_order')
                    ->orderBy('id')
                    ->get();
        } else {
            $rows = ProductCustomizationRow::activeOrdered($productId);
        }

        if ($techniques->isEmpty() || $rows->isEmpty()) {
            // Ürüne özel satır yoksa boş tablo; teknikler varsa yine de boş rows dön (fallback çeviri satırları gösterme).
            if ($productId !== null && ProductCustomizationRow::productPivotTableExists()) {
                return self::emptyCatalog($settings, $techniques);
            }

            return self::fallbackFromTranslations();
        }

        $printMap = [];
        foreach ($techniques as $technique) {
            $printMap[$technique->slug] = $technique->name;
        }

        $defaultSlug = (string) ($settings->default_print_technique_slug ?? '');
        if ($defaultSlug === '' || ! isset($printMap[$defaultSlug])) {
            $defaultSlug = (string) $techniques->first()->slug;
        }

        $maxColors = max(1, min(20, (int) ($settings->max_color_count ?? 7)));

        return [
            'max_color_count' => $maxColors,
            'default_print_slug' => $defaultSlug,
            'print_techniques' => $printMap,
            'rows' => $rows,
        ];
    }

    /**
     * @param  Collection<int, ProductCustomizationPrintTechnique>  $techniques
     * @return array{
     *     max_color_count: int,
     *     default_print_slug: string,
     *     print_techniques: array<string, string>,
     *     rows: Collection<int, ProductCustomizationRow>
     * }
     */
    private static function emptyCatalog(ProductCustomizationSetting $settings, Collection $techniques): array
    {
        $printMap = [];
        foreach ($techniques as $technique) {
            $printMap[$technique->slug] = $technique->name;
        }

        $defaultSlug = (string) ($settings->default_print_technique_slug ?? '');
        if (($defaultSlug === '' || ! isset($printMap[$defaultSlug])) && $techniques->isNotEmpty()) {
            $defaultSlug = (string) $techniques->first()->slug;
        }

        return [
            'max_color_count' => max(1, min(20, (int) ($settings->max_color_count ?? 7))),
            'default_print_slug' => $defaultSlug !== '' ? $defaultSlug : 'emprime',
            'print_techniques' => $printMap !== [] ? $printMap : [
                'emprime' => __('store.product.customization_print_emprime'),
            ],
            'rows' => collect(),
        ];
    }

    /**
     * @return array{
     *     max_color_count: int,
     *     default_print_slug: string,
     *     print_techniques: array<string, string>,
     *     rows: Collection<int, object>
     * }
     */
    private static function fallbackFromTranslations(): array
    {
        $printTechniques = [
            'emprime' => __('store.product.customization_print_emprime'),
            'dtf' => __('store.product.customization_print_dtf'),
            'direct_digital' => __('store.product.customization_print_direct_digital'),
            'embroidery' => __('store.product.customization_print_embroidery'),
        ];

        $rows = collect();
        for ($cr = 1; $cr <= 9; $cr++) {
            $dimsStr = (string) __('store.product.customization_row'.$cr.'_dims');
            $dimParts = preg_split('/\s*[×x]\s*/ui', trim($dimsStr), 2);
            $rows->push((object) [
                'id' => $cr,
                'position_name' => (string) __('store.product.customization_row'.$cr.'_konum'),
                'position_image' => null,
                'default_width' => isset($dimParts[0]) ? (float) str_replace(',', '.', trim($dimParts[0])) : null,
                'default_height' => isset($dimParts[1]) ? (float) str_replace(',', '.', trim($dimParts[1])) : null,
                'default_color_count' => max(1, min(7, (int) preg_replace('/\D/', '', (string) __('store.product.customization_row'.$cr.'_renk')) ?: 3)),
                'default_print_technique_slug' => 'emprime',
            ]);
        }

        return [
            'max_color_count' => 7,
            'default_print_slug' => 'emprime',
            'print_techniques' => $printTechniques,
            'rows' => $rows,
        ];
    }
}
