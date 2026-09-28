<?php

namespace App\Models;

use App\Models\Concerns\FillsLocalizedNameFromCatalog;
use App\Models\Concerns\HasLocalizedName;
use App\Models\Concerns\SyncsLinkedProductVariationOptions;
use App\Support\LocaleContent;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InterfaceDeliveryMethodVariation extends Model
{
    use FillsLocalizedNameFromCatalog;
    use HasLocalizedName;
    use SyncsLinkedProductVariationOptions;

    protected $table = 'interface_delivery_method_variations';

    protected $fillable = [
        'name',
        'name_en',
        'name_it',
        'description',
        'description_en',
        'description_it',
        'estimated_delivery_time',
        'estimated_delivery_time_en',
        'estimated_delivery_time_it',
        'image_path',
        'price_multiplier',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price_multiplier' => 'decimal:3',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function getLocalizedDescriptionAttribute(): string
    {
        return LocaleContent::display($this->description, $this->description_en ?? null, $this->description_it ?? null);
    }

    public function getLocalizedEstimatedDeliveryTimeAttribute(): string
    {
        return LocaleContent::display(
            $this->estimated_delivery_time,
            $this->estimated_delivery_time_en ?? null,
            $this->estimated_delivery_time_it ?? null,
        );
    }

    protected static function linkedProductVariationType(): string
    {
        return 'delivery_type';
    }

    public function productVariationOptions()
    {
        return $this->hasMany(ProductVariationOption::class, 'interface_delivery_method_variation_id');
    }

    public function subOptions(): HasMany
    {
        return $this->hasMany(
            InterfaceDeliveryMethodSubOption::class,
            'interface_delivery_method_variation_id'
        )->orderBy('sort_order')->orderBy('id');
    }

    /** Mağaza / ürün tarafında kullanım için aktif kayıtlar. */
    public static function forDisplay(): Collection
    {
        return static::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
    }
}
