<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class InterfacePackagingPreferenceVariation extends Model
{
    protected $table = 'interface_packaging_preference_variations';

    protected $fillable = [
        'name',
        'slug',
        'image_path',
        'requires_material',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'requires_material' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function productVariationOptions()
    {
        return $this->hasMany(ProductVariationOption::class, 'interface_packaging_preference_variation_id');
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
