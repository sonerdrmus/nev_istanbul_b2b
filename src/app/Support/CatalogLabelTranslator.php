<?php

namespace App\Support;

/**
 * Resolves Turkish storefront/admin catalog labels for the active locale.
 */
class CatalogLabelTranslator
{
    /** @var array<string, array{en?: string, it?: string}>|null */
    private static ?array $map = null;

    /** @var array<string, string>|null */
    private static ?array $lookup = null;

    public static function label(?string $text, ?string $locale = null): string
    {
        $text = trim((string) $text);
        if ($text === '') {
            return '';
        }

        $locale = $locale ?? app()->getLocale();
        if ($locale === 'tr' || ! in_array($locale, ['en', 'it'], true)) {
            return $text;
        }

        self::boot();

        $key = self::normalizeKey($text);
        if ($key !== '' && isset(self::$lookup[$key])) {
            $original = self::$lookup[$key];
            $translations = self::$map[$original] ?? [];
            if ($locale === 'en' && filled($translations['en'] ?? null)) {
                return (string) $translations['en'];
            }
            if ($locale === 'it') {
                if (filled($translations['it'] ?? null)) {
                    return (string) $translations['it'];
                }
                if (filled($translations['en'] ?? null)) {
                    return (string) $translations['en'];
                }
            }
        }

        return $text;
    }

    /**
     * Prefer explicit EN field, then catalog map, then source text.
     */
    public static function field(?string $source, ?string $en = null, ?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();
        $source = (string) ($source ?? '');

        if (in_array($locale, ['en', 'it'], true) && filled($en)) {
            return (string) $en;
        }

        return self::label($source, $locale);
    }

    private static function boot(): void
    {
        if (self::$map !== null) {
            return;
        }

        /** @var array<string, array{en?: string, it?: string}> $labels */
        $labels = (array) config('catalog_labels.labels', []);
        self::$map = $labels;
        self::$lookup = [];
        foreach (array_keys($labels) as $source) {
            self::$lookup[self::normalizeKey((string) $source)] = (string) $source;
        }
    }

    private static function normalizeKey(string $text): string
    {
        $text = mb_strtolower(trim($text), 'UTF-8');
        $text = preg_replace('/\s+/u', ' ', $text) ?? $text;

        return $text;
    }
}
