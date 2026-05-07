<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class InterfaceFabricTypeVariation extends Model
{
    protected $table = 'interface_fabric_type_variations';

    protected $fillable = [
        'name',
        'image_path',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function productVariationOptions()
    {
        return $this->hasMany(ProductVariationOption::class, 'interface_fabric_type_variation_id');
    }

    /** Mağaza / ürün varyasyonunda kullanım için aktif kayıtlar (görsel zorunlu). */
    public static function forDisplay(): Collection
    {
        return static::query()
            ->where('is_active', true)
            ->whereNotNull('image_path')
            ->where('image_path', '!=', '')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
    }
}
