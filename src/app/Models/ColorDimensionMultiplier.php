<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class ColorDimensionMultiplier extends Model
{
    protected $fillable = [
        'product_id',
        'print_technique_slug',
        'color_count',
        'multiplier_price',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'product_id' => 'integer',
            'color_count' => 'integer',
            'multiplier_price' => 'decimal:3',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /** @return Collection<int, static> */
    public static function activeOrdered(?string $printTechniqueSlug = null, ?int $productId = null): Collection
    {
        $query = static::query()
            ->when(
                $printTechniqueSlug !== null && $printTechniqueSlug !== '',
                fn ($q) => $q->where('print_technique_slug', $printTechniqueSlug),
            )
            ->where('is_active', true);

        $query = \App\Support\ProductDimensionMultiplierSync::applyProductScope(
            $query,
            'color_dimension_multipliers',
            $productId,
        );

        return $query->orderBy('sort_order')->orderBy('color_count')->get();
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
