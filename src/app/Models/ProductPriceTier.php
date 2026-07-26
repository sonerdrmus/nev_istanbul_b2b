<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductPriceTier extends Model
{
    protected $table = 'product_price_tiers';

    protected $fillable = [
        'product_id',
        'min_quantity',
        'max_quantity',
        'price_multiplier',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'min_quantity' => 'integer',
            'max_quantity' => 'integer',
            'price_multiplier' => 'decimal:4',
            'sort_order' => 'integer',
        ];
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function matchesQuantity(int $quantity): bool
    {
        if ($quantity < (int) $this->min_quantity) {
            return false;
        }

        if ($this->max_quantity === null) {
            return true;
        }

        return $quantity <= (int) $this->max_quantity;
    }

    /** ≤0 veya geçersiz çarpan → 1. */
    public function normalizedMultiplier(): float
    {
        $m = (float) $this->price_multiplier;

        return $m > 0.0 ? $m : 1.0;
    }
}
