<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class QuantityDimensionMultiplier extends Model
{
    protected $fillable = [
        'product_id',
        'print_technique_slug',
        'quantity_from',
        'quantity_to',
        'multiplier_price',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'product_id' => 'integer',
            'quantity_from' => 'integer',
            'quantity_to' => 'integer',
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
            'quantity_dimension_multipliers',
            $productId,
        );

        return $query->orderBy('sort_order')->orderBy('quantity_from')->get();
    }

    public function rangeLabel(): string
    {
        if ($this->quantity_to >= 9999) {
            return $this->quantity_from.'+';
        }

        return $this->quantity_from.' - '.$this->quantity_to;
    }

    /** @return array<string, mixed> */
    public static function repeaterRowFromModel(self $row): array
    {
        return [
            'id' => $row->id,
            'quantity_from' => $row->quantity_from,
            'quantity_to' => $row->quantity_to,
            'multiplier_price' => $row->multiplier_price,
            'sort_order' => $row->sort_order,
            'is_active' => $row->is_active,
        ];
    }
}
