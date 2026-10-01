<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomizationChoiceAssignment extends Model
{
    protected $fillable = [
        'customization_choice_id',
        'product_id',
        'after_product_variation_id',
    ];

    public function customizationChoice(): BelongsTo
    {
        return $this->belongsTo(CustomizationChoice::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function afterProductVariation(): BelongsTo
    {
        return $this->belongsTo(ProductVariation::class, 'after_product_variation_id');
    }
}
