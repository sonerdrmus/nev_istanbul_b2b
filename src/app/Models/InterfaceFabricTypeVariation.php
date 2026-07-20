<?php

namespace App\Models;

use App\Models\Concerns\SyncsLinkedProductVariationOptions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class InterfaceFabricTypeVariation extends Model
{
    use SyncsLinkedProductVariationOptions;

    protected $table = 'interface_fabric_type_variations';

    protected $fillable = [
        'name',
        'image_path',
        'detail_text',
        'price_multiplier',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price_multiplier' => 'decimal:4',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    protected static function linkedProductVariationType(): string
    {
        return 'fabric';
    }

    public function productVariationOptions()
    {
        return $this->hasMany(ProductVariationOption::class, 'interface_fabric_type_variation_id');
    }

    /** Bu kumaş türüne bağlı arayüz renk swatch'ları (Renk Varyasyonları, eski tek FK). */
    public function interfaceColorVariations()
    {
        return $this->hasMany(InterfaceColorVariation::class, 'interface_fabric_type_variation_id');
    }

    /** Renk Varyasyonları kayıtları (çoklu; pivot). */
    public function colorVariations()
    {
        return $this->belongsToMany(
            InterfaceColorVariation::class,
            'interface_color_variation_interface_fabric_type_variation',
            'interface_fabric_type_variation_id',
            'interface_color_variation_id',
        )->withTimestamps();
    }

    /** Mağaza / ürün varyasyonunda kullanım için aktif kayıtlar. */
    public static function forDisplay(): Collection
    {
        return static::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
    }
}
