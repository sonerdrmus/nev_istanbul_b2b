<?php

namespace App\Support;

use App\Models\SizeDimensionMultiplier;
use Illuminate\Support\Facades\Schema;

/**
 * Mağaza: özelleştirme alanı (cm²) → admin Ebat Çarpanı tablosu eşlemesi.
 */
final class SizeDimensionMultiplierCatalog
{
    /**
     * JS tarafında eşleme için aktif ebat satırları.
     *
     * @return list<array{size_label: string, ebat_cm2: float, fixed_multiplier: string|null, extra_multiplier: float}>
     */
    public static function rowsForStoreMatcher(): array
    {
        if (! Schema::hasTable('size_dimension_multipliers')) {
            return [];
        }

        return SizeDimensionMultiplier::activeOrdered()
            ->map(fn (SizeDimensionMultiplier $row): array => [
                'size_label' => (string) $row->size_label,
                'ebat_cm2' => round((float) $row->auto_multiplier, 2),
                'fixed_multiplier' => $row->fixed_multiplier !== null && $row->fixed_multiplier !== ''
                    ? (string) $row->fixed_multiplier
                    : null,
                'extra_multiplier' => round((float) $row->extra_multiplier, 4),
            ])
            ->values()
            ->all();
    }

    /**
     * @return array{size_label: string, ebat_cm2: float, fixed_multiplier: string|null, extra_multiplier: float}|null
     */
    public static function matchRowForAreaCm2(?float $areaCm2): ?array
    {
        if ($areaCm2 === null || $areaCm2 <= 0) {
            return null;
        }

        $rows = self::rowsForStoreMatcher();
        if ($rows === []) {
            return null;
        }

        usort($rows, fn (array $a, array $b): int => $a['ebat_cm2'] <=> $b['ebat_cm2']);

        foreach ($rows as $row) {
            if ($row['ebat_cm2'] >= $areaCm2 - 1e-9) {
                return $row;
            }
        }

        return $rows[array_key_last($rows)];
    }

    /**
     * Hesaplanan cm² için: Ebat cm² değeri alanı karşılayan en küçük (≥ alan) EBAT.
     * Örn. 50,4 cm² → A8 (31) yetmez, A7 (63) yeter → A7.
     */
    public static function matchEbatLabelForAreaCm2(?float $areaCm2): ?string
    {
        if ($areaCm2 === null || $areaCm2 <= 0) {
            return null;
        }

        $matched = self::matchRowForAreaCm2($areaCm2);

        return $matched['size_label'] ?? null;
    }
}
