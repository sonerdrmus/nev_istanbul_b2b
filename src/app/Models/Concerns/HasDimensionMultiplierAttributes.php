<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

trait HasDimensionMultiplierAttributes
{
    /** @return Collection<int, static> */
    public static function activeOrdered(?string $printTechniqueSlug = null): Collection
    {
        return static::query()
            ->when(
                $printTechniqueSlug !== null && $printTechniqueSlug !== '',
                fn ($query) => $query->where('print_technique_slug', $printTechniqueSlug),
            )
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
    }

    /** @return array<string, mixed> */
    public static function repeaterRowFromModel(Model $row): array
    {
        return [
            'id' => $row->id,
            'size_label' => $row->size_label,
            'width' => $row->width,
            'height' => $row->height,
            'auto_multiplier' => $row->auto_multiplier,
            'fixed_multiplier' => $row->fixed_multiplier,
            'extra_multiplier' => $row->extra_multiplier,
            'sort_order' => $row->sort_order,
            'is_active' => $row->is_active,
        ];
    }
}
