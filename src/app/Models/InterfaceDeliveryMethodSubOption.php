<?php

namespace App\Models;

use App\Models\Concerns\FillsLocalizedNameFromCatalog;
use App\Models\Concerns\HasLocalizedName;
use App\Support\LocaleContent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InterfaceDeliveryMethodSubOption extends Model
{
    use FillsLocalizedNameFromCatalog;
    use HasLocalizedName;
    protected $table = 'interface_delivery_method_sub_options';

    protected $fillable = [
        'interface_delivery_method_variation_id',
        'name',
        'name_en',
        'name_it',
        'description',
        'description_en',
        'description_it',
        'price_multiplier',
        'sort_order',
        'is_default',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price_multiplier' => 'decimal:3',
            'sort_order' => 'integer',
            'is_default' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function getLocalizedDescriptionAttribute(): string
    {
        return LocaleContent::display($this->description, $this->description_en ?? null, $this->description_it ?? null);
    }

    public function deliveryMethod(): BelongsTo
    {
        return $this->belongsTo(
            InterfaceDeliveryMethodVariation::class,
            'interface_delivery_method_variation_id'
        );
    }
}
