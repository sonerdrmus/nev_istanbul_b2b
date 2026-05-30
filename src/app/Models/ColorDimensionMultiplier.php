<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class ColorDimensionMultiplier extends Model
{
    protected $fillable = [
        'color_count',
        'multiplier_price',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'color_count' => 'integer',
            'multiplier_price' => 'decimal:4',
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
            ->orderBy('color_count')
            ->get();
    }

    /** @return array<string, mixed> */
    public static function repeaterRowFromModel(self $row): array
    {
        return [
            'id' => $row->id,
            'color_count' => $row->color_count,
            'multiplier_price' => $row->multiplier_price,
            'sort_order' => $row->sort_order,
            'is_active' => $row->is_active,
        ];
    }
}
