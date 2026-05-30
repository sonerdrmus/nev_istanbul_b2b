<?php

namespace App\Support;

use App\Models\ColorDimensionMultiplier;
use Illuminate\Support\Facades\Schema;

/**
 * Mağaza: baskı satırı renk sayısı → admin Renk Çarpanı tablosu eşlemesi.
 */
final class ColorDimensionMultiplierCatalog
{
    /**
     * @return list<array{color_count: int, multiplier_price: float}>
     */
    public static function rowsForStoreMatcher(): array
    {
        if (! Schema::hasTable('color_dimension_multipliers')) {
            return [];
        }

        return ColorDimensionMultiplier::activeOrdered()
            ->map(fn (ColorDimensionMultiplier $row): array => [
                'color_count' => (int) $row->color_count,
                'multiplier_price' => round((float) $row->multiplier_price, 4),
            ])
            ->values()
            ->all();
    }

    /**
     * @return array{color_count: int, multiplier_price: float}|null
     */
    public static function matchRowForColorCount(?int $colorCount): ?array
    {
        if ($colorCount === null || $colorCount <= 0) {
            return null;
        }

        foreach (self::rowsForStoreMatcher() as $row) {
            if ($row['color_count'] === $colorCount) {
                return $row;
            }
        }

        return null;
    }
}
