<?php

namespace App\Support;

/**
 * Resolves Turkish storefront/admin catalog labels for the active locale.
 */
class CatalogLabelTranslator
{
    /** @var array<string, array{tr?: string, en?: string, it?: string}>|null */
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
        if (! in_array($locale, ['tr', 'en', 'it'], true)) {
            return $text;
        }

        self::boot();

        $key = self::normalizeKey($text);
        if ($key !== '' && isset(self::$lookup[$key])) {
            $original = self::$lookup[$key];
            $translations = self::$map[$original] ?? [];
            if ($locale === 'tr' && filled($translations['tr'] ?? null)) {
                return (string) $translations['tr'];
            }
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
     * Prefer explicit locale field, then catalog map, then TR source.
     */
    public static function field(?string $source, ?string $en = null, ?string $it = null, ?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();
        $source = (string) ($source ?? '');

        if ($locale === 'en' && filled($en)) {
            return (string) $en;
        }

        if ($locale === 'it') {
            if (filled($it)) {
                return (string) $it;
            }
            if (filled($en)) {
                return (string) $en;
            }
        }

        return self::label($source, $locale);
    }

    /**
     * @return array{en: string, it: string}
     */
    public static function pair(?string $source): array
    {
        $source = trim((string) $source);
        if ($source === '') {
            return ['en' => '', 'it' => ''];
        }

        return [
            'en' => self::label($source, 'en'),
            'it' => self::label($source, 'it'),
        ];
    }

    /**
     * Keep existing translations; otherwise catalog map or the TR source.
     *
     * @return array{en: string, it: string}
     */
    public static function fillPair(?string $source, ?string $en = null, ?string $it = null): array
    {
        $pair = self::pair($source);

        return [
            'en' => filled($en) ? (string) $en : $pair['en'],
            'it' => filled($it) ? (string) $it : $pair['it'],
        ];
    }

    private static function boot(): void
    {
        if (self::$map !== null) {
            return;
        }

        /** @var array<string, array{tr?: string, en?: string, it?: string}> $labels */
        $labels = (array) config('catalog_labels.labels', []);
        self::$map = $labels;
        self::$lookup = [];
        foreach (array_keys($labels) as $source) {
            self::$lookup[self::normalizeKey((string) $source)] = (string) $source;
        }
    }

    private static function normalizeKey(string $text): string
    {
        $text = str_replace(["\u{200B}", "\u{200C}", "\u{200D}", "\u{00A0}", "\u{FEFF}"], '', $text);
        $text = mb_strtolower(trim($text), 'UTF-8');
        $text = preg_replace('/\s+/u', ' ', $text) ?? $text;

        return $text;
    }
}
