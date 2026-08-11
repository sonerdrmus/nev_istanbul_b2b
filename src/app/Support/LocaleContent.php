<?php

namespace App\Support;

/**
 * Shared helpers for bilingual (TR primary + EN) content fields.
 */
class LocaleContent
{
    /**
     * Display name/label for the active locale.
     * EN/IT prefer *En field, then catalog map, then TR source.
     */
    public static function display(?string $tr, ?string $en = null, ?string $locale = null): string
    {
        return CatalogLabelTranslator::field($tr, $en, $locale);
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
