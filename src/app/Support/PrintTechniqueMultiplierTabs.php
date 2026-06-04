<?php

namespace App\Support;

use App\Models\ProductCustomizationPrintTechnique;
use Illuminate\Support\Facades\Schema;

/**
 * Ebat Çarpan Yönetimi sekmeleri: Ürün Özelleştirme → Baskı teknikleri listesinden.
 */
final class PrintTechniqueMultiplierTabs
{
    /**
     * @return list<array{slug: string, label: string}>
     */
    public static function definitions(): array
    {
        if (! Schema::hasTable('product_customization_print_techniques')) {
            return self::fallbackDefinitions();
        }

        $techniques = ProductCustomizationPrintTechnique::query()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        if ($techniques->isEmpty()) {
            return self::fallbackDefinitions();
        }

        $definitions = [];
        $seenCanonical = [];

        foreach ($techniques as $technique) {
            $canonical = PrintTechniqueSlugResolver::canonical($technique->slug);

            if (isset($seenCanonical[$canonical])) {
                continue;
            }

            $seenCanonical[$canonical] = true;
            $definitions[] = [
                'slug' => $canonical,
                'label' => trim((string) $technique->name) !== ''
                    ? (string) $technique->name
                    : PrintTechniqueDimensionMultiplierTypes::label($canonical),
            ];
        }

        return $definitions !== [] ? $definitions : self::fallbackDefinitions();
    }

    /** @return list<string> */
    public static function slugs(): array
    {
        return array_column(self::definitions(), 'slug');
    }

    /** @return array<string, string> */
    public static function labelsBySlug(): array
    {
        $labels = [];

        foreach (self::definitions() as $definition) {
            $labels[$definition['slug']] = $definition['label'];
        }

        return $labels;
    }

    /**
     * @return list<array{slug: string, label: string}>
     */
    private static function fallbackDefinitions(): array
    {
        $definitions = [];

        foreach (PrintTechniqueDimensionMultiplierTypes::labels() as $slug => $label) {
            $definitions[] = [
                'slug' => $slug,
                'label' => $label,
            ];
        }

        return $definitions;
    }
}
