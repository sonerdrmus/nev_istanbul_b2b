<?php

namespace App\Models;

use App\Models\Concerns\HasDimensionMultiplierAttributes;
use Illuminate\Database\Eloquent\Model;

class SizeDimensionMultiplier extends Model
{
    use HasDimensionMultiplierAttributes;

    protected $fillable = [
        'product_id',
        'print_technique_slug',
        'size_label',
        'width',
        'height',
        'auto_multiplier',
        'fixed_multiplier',
        'extra_multiplier',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'product_id' => 'integer',
            'width' => 'decimal:2',
            'height' => 'decimal:2',
            'auto_multiplier' => 'decimal:2',
            'extra_multiplier' => 'decimal:4',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
