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

    /**
     * Seçilen varyasyon değerlerine göre fiyat farkı toplamı ve detayını döndürür.
     *
     * @return array{delta_total: float, breakdown: array<string, array<string, float>>}
     */
    public static function forSelection(Product $product, array $selections): array
    {
        $product->loadMissing('variations.options');

        $deltaTotal = 0.0;
        $breakdown = [];

        foreach ($selections as $variationName => $optionValue) {
            $variation = $product->variations->firstWhere('name', (string) $variationName);
            if (! $variation) {
                continue;
            }
            $option = $variation->options->firstWhere('option_value', (string) $optionValue);
            if (! $option) {
                continue;
            }
            $delta = (float) $option->price_delta;
            if ($delta === 0.0) {
                continue;
            }
            $deltaTotal += $delta;
            $breakdown[(string) $variationName] = [(string) $optionValue => $delta];
        }

        return ['delta_total' => $deltaTotal, 'breakdown' => $breakdown];
    }
}
