<?php

namespace App\Support;

/**
 * Baskı tekniği bazlı ebat / adet / renk çarpan tabloları.
 */
final class PrintTechniqueDimensionMultiplierTypes
{
    public const SLUG_EMPRIME = 'emprime';

    public const SLUG_DTF = 'dtf';

    public const SLUG_EMBROIDERY = 'embroidery';

    public const SLUG_DIRECT_DIGITAL = 'direct_digital';

    /** @return list<string> */
    public static function slugs(): array
    {
        return [
            self::SLUG_EMPRIME,
            self::SLUG_DTF,
            self::SLUG_EMBROIDERY,
            self::SLUG_DIRECT_DIGITAL,
        ];
    }

    /** @return array<string, string> slug => admin sekme etiketi */
    public static function labels(): array
    {
        return [
            self::SLUG_EMPRIME => 'Emprime Baskı',
            self::SLUG_DTF => 'DTF Baskı',
            self::SLUG_EMBROIDERY => 'Nakış Baskı',
            self::SLUG_DIRECT_DIGITAL => 'Dijital Baskı',
        ];
    }

    public static function label(string $slug): string
    {
        return self::labels()[$slug] ?? $slug;
    }

    public static function supportsColorMultiplier(string $slug): bool
    {
        return $slug === self::SLUG_EMPRIME;
    }

    public static function isValidSlug(string $slug): bool
    {
        return in_array($slug, self::slugs(), true);
    }
}
