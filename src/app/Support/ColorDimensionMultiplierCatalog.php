<?php

namespace App\Support;

use App\Models\ColorDimensionMultiplier;
use App\Support\PrintTechniqueDimensionMultiplierTypes;
use Illuminate\Support\Facades\Schema;

/**
 * Mağaza: baskı satırı renk sayısı → admin Renk Çarpanı tablosu eşlemesi.
 */
final class ColorDimensionMultiplierCatalog
{
    /**
     * @return list<array{color_count: int, multiplier_price: float}>
     */
    public static function rowsForStoreMatcher(?string $printTechniqueSlug = null, ?int $productId = null): array
    {
        if (! Schema::hasTable('color_dimension_multipliers')) {
            return [];
        }

        $slug = self::resolvePrintTechniqueSlug($printTechniqueSlug);
        if ($slug === null || ! PrintTechniqueDimensionMultiplierTypes::supportsColorMultiplier($slug)) {
            return [];
        }

        return ColorDimensionMultiplier::activeOrdered($slug, $productId)
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
    public static function matchRowForColorCount(?int $colorCount, ?string $printTechniqueSlug = null, ?int $productId = null): ?array
    {
        if ($colorCount === null || $colorCount <= 0) {
            return null;
        }

        foreach (self::rowsForStoreMatcher($printTechniqueSlug, $productId) as $row) {
            if ($row['color_count'] === $colorCount) {
                return $row;
            }
        }

        return null;
    }

    private static function resolvePrintTechniqueSlug(?string $printTechniqueSlug): ?string
    {
        if (! DimensionMultiplierCatalog::hasPrintTechniqueColumn()) {
            return PrintTechniqueDimensionMultiplierTypes::SLUG_EMPRIME;
        }

        return PrintTechniqueSlugResolver::canonical($printTechniqueSlug);
    }
}
