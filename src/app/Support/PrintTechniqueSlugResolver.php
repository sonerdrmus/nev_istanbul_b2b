<?php

namespace App\Support;

/**
 * Mağaza baskı tekniği slug'larını çarpan tablolarındaki kanonik slug'lara eşler.
 * Örn. admin/ürün özelleştirmede "direct-digital" → çarpanlarda "direct_digital".
 */
final class PrintTechniqueSlugResolver
{
    /** @return array<string, string> */
    private static function aliasMap(): array
    {
        return [
            'dijital' => PrintTechniqueDimensionMultiplierTypes::SLUG_DIRECT_DIGITAL,
            'dijital_baski' => PrintTechniqueDimensionMultiplierTypes::SLUG_DIRECT_DIGITAL,
            'digital' => PrintTechniqueDimensionMultiplierTypes::SLUG_DIRECT_DIGITAL,
            'digital_print' => PrintTechniqueDimensionMultiplierTypes::SLUG_DIRECT_DIGITAL,
        ];
    }

    /**
     * Slug normalizasyonu: tire → alt çizgi, bilinen takma adlar.
     * Yeni baskı teknikleri kendi slug'ında kalır (emprime'e dönüştürülmez).
     */
    public static function canonical(?string $slug): string
    {
        if ($slug === null || trim($slug) === '') {
            return PrintTechniqueDimensionMultiplierTypes::SLUG_EMPRIME;
        }

        $key = self::normalizeKey($slug);

        return self::aliasMap()[$key] ?? $key;
    }

    /**
     * Mağaza select değerleri → kanonik çarpan slug (JS için).
     *
     * @param  array<string, string>  $storePrintTechniques  slug => label
     * @return array<string, string>
     */
    public static function canonicalMapForStoreSlugs(array $storePrintTechniques): array
    {
        $map = [];

        foreach (array_keys($storePrintTechniques) as $storeSlug) {
            $map[(string) $storeSlug] = self::canonical((string) $storeSlug);
        }

        foreach (PrintTechniqueDimensionMultiplierTypes::slugs() as $canonical) {
            $map[$canonical] = $canonical;
        }

        return $map;
    }

    public static function normalizeKey(string $slug): string
    {
        return strtolower(trim(str_replace('-', '_', $slug)));
    }
}
