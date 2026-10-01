<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExtraVariationAssignment extends Model
{
    protected $fillable = [
        'extra_variation_id',
        'product_id',
        'after_product_variation_id',
    ];

    public function extraVariation(): BelongsTo
    {
        return $this->belongsTo(ExtraVariation::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function afterVariation(): BelongsTo
    {
        return $this->belongsTo(ProductVariation::class, 'after_product_variation_id');
    }
}
