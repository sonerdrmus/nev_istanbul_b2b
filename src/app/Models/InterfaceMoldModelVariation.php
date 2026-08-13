<?php

namespace App\Models;

use App\Models\Concerns\SyncsLinkedProductVariationOptions;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class InterfaceMoldModelVariation extends Model
{
    use SyncsLinkedProductVariationOptions;

    protected $table = 'interface_mold_model_variations';

    protected $fillable = [
        'name',
        'image_path',
        'size_table_image_path',
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

    protected static function linkedProductVariationType(): string
    {
        return 'mold_model_type';
    }

    public function productVariationOptions()
    {
        return $this->hasMany(ProductVariationOption::class, 'interface_mold_model_variation_id');
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
