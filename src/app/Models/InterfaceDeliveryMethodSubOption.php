<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InterfaceDeliveryMethodSubOption extends Model
{
    protected $table = 'interface_delivery_method_sub_options';

    protected $fillable = [
        'interface_delivery_method_variation_id',
        'name',
        'description',
        'price_multiplier',
        'sort_order',
        'is_default',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price_multiplier' => 'decimal:4',
            'sort_order' => 'integer',
            'is_default' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function deliveryMethod(): BelongsTo
    {
        return $this->belongsTo(
            InterfaceDeliveryMethodVariation::class,
            'interface_delivery_method_variation_id'
        );
    }
}
