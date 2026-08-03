<?php

namespace App\Support;

/**
 * Sepet / sipariş satırına eklenecek baskı (ürün özelleştirme) toplamı (TRY).
 *
 * tryFromVariationData: birim (adet başına) baskı toplamı.
 * lineTotalTryFromVariationData: birim × sipariş adedi.
 */
final class ProductCustomizationPrintTotal
{
    /**
     * variation_data içindeki product_customization_table satırlarından birim baskı toplamını okur.
     */
    public static function tryFromVariationData(mixed $variationData): float
    {
        if (! is_array($variationData) || $variationData === []) {
            return 0.0;
        }

        if (($variationData['product_customization'] ?? null) === 'skipped') {
            return 0.0;
        }

        $table = $variationData['product_customization_table'] ?? null;
        if (! is_array($table)) {
            return 0.0;
        }

        if (isset($table['print_total_try']) && is_numeric($table['print_total_try'])) {
            return max(0.0, round((float) $table['print_total_try'], 4));
        }

        $sum = 0.0;
        $hasAny = false;
        foreach ($table['rows'] ?? [] as $row) {
            if (! is_array($row)) {
                continue;
            }
            if (! isset($row['total_price_try']) || ! is_numeric($row['total_price_try'])) {
                continue;
            }
            $sum += (float) $row['total_price_try'];
            $hasAny = true;
        }

        return $hasAny ? max(0.0, round($sum, 4)) : 0.0;
    }

    /**
     * Birim baskı toplamı × sipariş adedi (sipariş/satır tutarına eklenecek tutar).
     */
    public static function lineTotalTryFromVariationData(mixed $variationData, int $orderQuantity): float
    {
        $unit = self::tryFromVariationData($variationData);
        if ($unit <= 0.0 || $orderQuantity < 1) {
            return 0.0;
        }

        return max(0.0, round($unit * $orderQuantity, 4));
    }
}
