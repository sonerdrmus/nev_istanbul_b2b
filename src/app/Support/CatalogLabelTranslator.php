<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Throwable;

/**
 * Resolves Turkish storefront/admin catalog labels for the active locale.
 * Panel-entered EN/IT fields override the static catalog map.
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

    /**
     * Source label => active-locale text, for storefront JavaScript.
     *
     * @return array<string, string>
     */
    public static function frontendMap(?string $locale = null): array
    {
        self::boot();
        $locale = $locale ?? app()->getLocale();
        $out = [];
        foreach (array_keys(self::$map ?? []) as $source) {
            $translated = self::label((string) $source, $locale);
            if ($translated === '') {
                continue;
            }
            $out[(string) $source] = $translated;
            $out[mb_strtolower((string) $source, 'UTF-8')] = $translated;
        }

        return $out;
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

        self::overlayStoredTranslations();
    }

    /**
     * Panel translations win over the static map when EN/IT is filled.
     */
    private static function overlayStoredTranslations(): void
    {
        $pairs = [
            ['products', 'name', 'name_en', 'name_it'],
            ['categories', 'name', 'name_en', 'name_it'],
            ['product_variations', 'name', 'name_en', 'name_it'],
            ['product_variation_options', 'option_value', 'option_value_en', 'option_value_it'],
            ['size_tables', 'name', 'name_en', 'name_it'],
            ['size_tables', 'title', 'title_en', 'title_it'],
            ['product_customization_rows', 'position_name', 'position_name_en', 'position_name_it'],
            ['product_customization_print_techniques', 'name', 'name_en', 'name_it'],
            ['footer_menu_groups', 'title', 'title_en', 'title_it'],
            ['footer_menu_items', 'label', 'label_en', 'label_it'],
            ['legal_pages', 'title', 'title_en', 'title_it'],
            ['banner_slides', 'title', 'title_en', 'title_it'],
            ['banner_slides', 'headline', 'headline_en', 'headline_it'],
            ['banner_slides', 'button_text', 'button_text_en', 'button_text_it'],
            ['interface_fabric_type_variations', 'name', 'name_en', 'name_it'],
            ['interface_color_variations', 'name', 'name_en', 'name_it'],
            ['interface_certificate_variations', 'name', 'name_en', 'name_it'],
            ['interface_mold_model_variations', 'name', 'name_en', 'name_it'],
            ['interface_delivery_method_variations', 'name', 'name_en', 'name_it'],
            ['interface_delivery_method_sub_options', 'name', 'name_en', 'name_it'],
            ['interface_packaging_preference_variations', 'name', 'name_en', 'name_it'],
            ['interface_label_type_variations', 'name', 'name_en', 'name_it'],
            ['interface_label_type_variations', 'description_title', 'description_title_en', 'description_title_it'],
            ['interface_packaging_materials', 'name', 'name_en', 'name_it'],
            ['interface_packaging_customizations', 'name', 'name_en', 'name_it'],
        ];

        try {
            foreach ($pairs as [$table, $sourceColumn, $enColumn, $itColumn]) {
                if (! Schema::hasTable($table) || ! Schema::hasColumn($table, $sourceColumn) || ! Schema::hasColumn($table, $enColumn)) {
                    continue;
                }

                $hasIt = Schema::hasColumn($table, $itColumn);
                $columns = $hasIt ? [$sourceColumn, $enColumn, $itColumn] : [$sourceColumn, $enColumn];
                $rows = DB::table($table)->select($columns)->whereNotNull($sourceColumn)->get();
                foreach ($rows as $row) {
                    $source = trim((string) ($row->{$sourceColumn} ?? ''));
                    $en = trim((string) ($row->{$enColumn} ?? ''));
                    $it = $hasIt ? trim((string) ($row->{$itColumn} ?? '')) : '';
                    if ($source === '' || ($en === '' && $it === '')) {
                        continue;
                    }
                    self::remember($source, $en, $it);
                }
            }
        } catch (Throwable) {
            // Storefront still falls back to the static catalog map.
        }
    }

    private static function remember(string $source, string $en, string $it): void
    {
        $key = self::normalizeKey($source);
        if ($key === '') {
            return;
        }

        $original = self::$lookup[$key] ?? $source;
        self::$lookup[$key] = $original;
        $existing = self::$map[$original] ?? [];
        if ($en !== '') {
            $existing['en'] = $en;
        }
        if ($it !== '') {
            $existing['it'] = $it;
        }
        self::$map[$original] = $existing;
    }

    private static function normalizeKey(string $text): string
    {
        $text = str_replace(["\u{200B}", "\u{200C}", "\u{200D}", "\u{00A0}", "\u{FEFF}"], '', $text);
        $text = mb_strtolower(trim($text), 'UTF-8');
        $text = preg_replace('/\s+/u', ' ', $text) ?? $text;

        return $text;
    }
}
