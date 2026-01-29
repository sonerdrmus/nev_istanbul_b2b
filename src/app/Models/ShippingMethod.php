<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingMethod extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'free_shipping_min_amount',
        'estimated_days',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'free_shipping_min_amount' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Sepet tutarına göre kargo ücreti (TL). Ücretsiz kargo limiti varsa ve tutar yeterliyse 0 döner.
     */
    public function getCostForCartTotal(float $cartTotalTRY): float
    {
        if ($this->free_shipping_min_amount !== null && (float) $this->free_shipping_min_amount > 0 && $cartTotalTRY >= (float) $this->free_shipping_min_amount) {
            return 0.0;
        }
        return (float) $this->price;
    }
}
