<?php

namespace App\Support;

/**
 * Shared helpers for TR primary + EN/IT display fields.
 */
class LocaleContent
{
    /**
     * Display name/label for the active locale.
     * Prefers the locale field, then catalog map, then the TR source.
     */
    public static function display(?string $tr, ?string $en = null, ?string $it = null, ?string $locale = null): string
    {
        return CatalogLabelTranslator::field($tr, $en, $it, $locale);
    }

    public static function fillEnglishFromTurkish(?string $tr, bool $isHtml = false): ?string
    {
        if (! filled($tr)) {
            return null;
        }

        return $isHtml
            ? MachineTranslator::translateHtml((string) $tr, 'tr', 'en')
            : MachineTranslator::translate((string) $tr, 'tr', 'en');
    }
}
