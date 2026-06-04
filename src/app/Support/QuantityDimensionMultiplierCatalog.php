<?php

namespace App\Support;

use App\Models\QuantityDimensionMultiplier;
use Illuminate\Support\Facades\Schema;

/**
 * Mağaza: sipariş adeti → admin Adet Çarpanı tablosu eşlemesi.
 */
final class QuantityDimensionMultiplierCatalog
{
    /**
     * @return list<array{quantity_from: int, quantity_to: int, multiplier_price: float}>
     */
    public static function rowsForStoreMatcher(?string $printTechniqueSlug = null): array
    {
        if (! Schema::hasTable('quantity_dimension_multipliers')) {
            return [];
        }

        $slug = self::resolvePrintTechniqueSlug($printTechniqueSlug);

        return QuantityDimensionMultiplier::activeOrdered($slug)
            ->map(fn (QuantityDimensionMultiplier $row): array => [
                'quantity_from' => (int) $row->quantity_from,
                'quantity_to' => (int) $row->quantity_to,
                'multiplier_price' => round((float) $row->multiplier_price, 4),
            ])
            ->values()
            ->all();
    }

    /**
     * @return array{quantity_from: int, quantity_to: int, multiplier_price: float}|null
     */
    public static function matchRowForQuantity(?int $quantity, ?string $printTechniqueSlug = null): ?array
    {
        if ($quantity === null || $quantity <= 0) {
            return null;
        }

        $rows = self::rowsForStoreMatcher($printTechniqueSlug);
        if ($rows === []) {
            return null;
        }

        foreach ($rows as $row) {
            if ($quantity >= $row['quantity_from'] && $quantity <= $row['quantity_to']) {
                return $row;
            }
        }

        $fallback = null;
        foreach ($rows as $row) {
            if ($quantity >= $row['quantity_from']) {
                $fallback = $row;
            }
        }

        return $fallback;
    }

    private static function resolvePrintTechniqueSlug(?string $printTechniqueSlug): ?string
    {
        if (! DimensionMultiplierCatalog::hasPrintTechniqueColumn()) {
            return null;
        }

        return PrintTechniqueSlugResolver::canonical($printTechniqueSlug);
    }
}
