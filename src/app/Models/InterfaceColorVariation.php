<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class InterfaceColorVariation extends Model
{
    protected $table = 'interface_color_variations';

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
        return $this->hasMany(ProductVariationOption::class, 'interface_color_variation_id');
    }

    /** Mağaza / arayüzde gösterim sırasıyla aktif kayıtlar. */
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
