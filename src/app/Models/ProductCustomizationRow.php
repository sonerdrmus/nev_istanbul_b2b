<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class ProductCustomizationRow extends Model
{
    protected $fillable = [
        'position_name',
        'default_width',
        'default_height',
        'default_color_count',
        'default_print_technique_slug',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'default_width' => 'decimal:2',
            'default_height' => 'decimal:2',
            'default_color_count' => 'integer',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    /** @return Collection<int, static> */
    public static function activeOrdered(): Collection
    {
        return static::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
    }
}
