<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariationOption extends Model
{
    protected $fillable = [
        'product_variation_id',
        'interface_color_variation_id',
        'interface_fabric_type_variation_id',
        'option_value',
        'option_color',
        'option_image',
        'option_image_size',
        'price_delta',
        'stock_quantity',
        'parent_option_id',
        'parent_option_ids',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'price_delta' => 'decimal:2',
            'parent_option_ids' => 'array',
        ];
    }

    /** Seçenek hangi üst seçenek(ler)e bağlı – hem tek parent_option_id hem parent_option_ids desteklenir. */
    public function getParentOptionIdsList(): array
    {
        $ids = $this->parent_option_ids ?? [];
        if (is_array($ids)) {
            $ids = array_filter(array_map('intval', $ids));
        } else {
            $ids = [];
        }
        if ($this->parent_option_id && ! in_array((int) $this->parent_option_id, $ids, true)) {
            $ids[] = (int) $this->parent_option_id;
        }

        return $ids;
    }

    public function variation()
    {
        return $this->belongsTo(ProductVariation::class, 'product_variation_id');
    }

    public function interfaceColorVariation()
    {
        return $this->belongsTo(InterfaceColorVariation::class, 'interface_color_variation_id');
    }

    public function interfaceFabricTypeVariation()
    {
        return $this->belongsTo(InterfaceFabricTypeVariation::class, 'interface_fabric_type_variation_id');
    }

    public function parentOption()
    {
        return $this->belongsTo(ProductVariationOption::class, 'parent_option_id');
    }

    public function childOptions()
    {
        return $this->hasMany(ProductVariationOption::class, 'parent_option_id');
    }

    /** 0 veya geçersiz değer = fiyatı değiştirmez (×1). */
    public static function normalizePriceMultiplier(mixed $raw): float
    {
        $f = (float) $raw;

        return $f > 0.0 ? $f : 1.0;
    }

    /**
     * Seçilen her seçenek için `price_delta` alanını çarpan olarak çarpımına çevirir (ör. 1,5 × 2 = 3).
     */
    public static function combinedMultiplierForSelections(Product $product, array $selections): float
    {
        $product->loadMissing('variations.options');

        $factor = 1.0;

        foreach ($selections as $variationName => $optionValue) {
            if ((string) $variationName === 'size_quantities') {
                continue;
            }

            $variation = $product->variations->firstWhere('name', (string) $variationName);
            if (! $variation) {
                continue;
            }

            if (is_array($optionValue)) {
                foreach ($optionValue as $v) {
                    if ($v === null || trim((string) $v) === '') {
                        continue;
                    }
                    $option = $variation->options->firstWhere('option_value', (string) $v);
                    if ($option) {
                        $factor *= self::normalizePriceMultiplier($option->price_delta);
                    }
                }
            } else {
                $option = $variation->options->firstWhere('option_value', (string) $optionValue);
                if ($option) {
                    $factor *= self::normalizePriceMultiplier($option->price_delta);
                }
            }
        }

        return $factor;
    }

    /**
     * Seçilen varyasyonlara göre çarpan bilgisi (TL farkı için `delta_total` artık kullanılmaz).
     *
     * @return array{multiplier_total: float, delta_total: float, breakdown: array<string, array<string, float>>}
     */
    public static function forSelection(Product $product, array $selections): array
    {
        $factor = self::combinedMultiplierForSelections($product, $selections);

        return [
            'multiplier_total' => $factor,
            'delta_total' => 0.0,
            'breakdown' => [],
        ];
    }
}
