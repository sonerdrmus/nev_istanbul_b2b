<?php

namespace App\Support;

use Illuminate\Support\Facades\Schema;

/**
 * Mağaza: baskı tekniğine göre gruplanmış çarpan tabloları.
 */
final class DimensionMultiplierCatalog
{
    /**
     * @return array<string, array{size: list<array<string, mixed>>, quantity: list<array<string, mixed>>, color: list<array<string, mixed>>}>
     */
    public static function groupedForStore(): array
    {
        $grouped = [];

        foreach (PrintTechniqueMultiplierTabs::slugs() as $slug) {
            $grouped[$slug] = [
                'size' => SizeDimensionMultiplierCatalog::rowsForStoreMatcher($slug),
                'quantity' => QuantityDimensionMultiplierCatalog::rowsForStoreMatcher($slug),
                'color' => PrintTechniqueDimensionMultiplierTypes::supportsColorMultiplier($slug)
                    ? ColorDimensionMultiplierCatalog::rowsForStoreMatcher($slug)
                    : [],
            ];
        }

        return $grouped;
    }

    public static function hasPrintTechniqueColumn(): bool
    {
        return Schema::hasTable('size_dimension_multipliers')
            && Schema::hasColumn('size_dimension_multipliers', 'print_technique_slug');
    }
}
