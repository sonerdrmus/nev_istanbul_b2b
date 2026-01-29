<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariationOptionPrice extends Model
{
    protected $fillable = [
        'product_variation_id',
        'option_value',
        'price_delta_try',
        'stock_quantity',
    ];

    protected function casts(): array
    {
        return [
            'price_delta_try' => 'decimal:2',
        ];
    }

    public function variation()
    {
        return $this->belongsTo(ProductVariation::class, 'product_variation_id');
    }

    /**
     * @return array{delta_total: float, breakdown: array<string, array<string, float>>}
     */
    public static function forSelection(Product $product, array $selections): array
    {
        $product->loadMissing('variations.optionPrices');

        $deltaTotal = 0.0;
        $breakdown = [];

        foreach ($selections as $variationName => $optionValue) {
            $variation = $product->variations->firstWhere('name', (string) $variationName);
            if (! $variation) {
                continue;
            }
            $priceRow = $variation->optionPrices->firstWhere('option_value', (string) $optionValue);
            if (! $priceRow) {
                continue;
            }
            $delta = (float) $priceRow->price_delta_try;
            if ($delta === 0.0) {
                continue;
            }
            $deltaTotal += $delta;
            $breakdown[(string) $variationName] = [(string) $optionValue => $delta];
        }

        return ['delta_total' => $deltaTotal, 'breakdown' => $breakdown];
    }
}

