<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class InterfaceLabelTypeVariation extends Model
{
    protected $table = 'interface_label_type_variations';

    protected $fillable = [
        'name',
        'image_path',
        'is_custom_print',
        'position_front',
        'position_back',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_custom_print' => 'boolean',
            'position_front' => 'boolean',
            'position_back' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
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
