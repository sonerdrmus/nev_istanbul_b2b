<?php

namespace App\Support;

class PackagingTypeVariationDisplay
{
    public static function isPackagingTypeSelection(mixed $value): bool
    {
        return is_array($value)
            && array_key_exists('option', $value)
            && (array_key_exists('customization', $value) || array_key_exists('material', $value) || array_key_exists('barcode_area', $value));
    }

    public static function formatVariationValue(mixed $value): ?string
    {
        if (! self::isPackagingTypeSelection($value)) {
            return null;
        }

        return self::formatPackagingSelection($value);
    }

    public static function formatPackagingSelection(array $value): string
    {
        $parts = [trim((string) ($value['option'] ?? ''))];

        if (! empty($value['material'])) {
            $parts[] = __('store.product.packaging_material_summary', [
                'material' => (string) $value['material'],
            ]);
        }

        if (! empty($value['customization_label'])) {
            $parts[] = (string) $value['customization_label'];
        } elseif (! empty($value['customization'])) {
            $parts[] = (string) $value['customization'];
        }

        if (! empty($value['barcode_area'])) {
            $parts[] = __('store.product.packaging_barcode_summary_yes');
        }

        return implode(' · ', array_filter($parts, static fn ($part) => $part !== ''));
    }

    /** @param  array<string, mixed>  $selections */
    public static function additiveExtraTryFromSelections(array $selections): float
    {
        $extra = 0.0;

        foreach ($selections as $value) {
            if (! is_array($value)) {
                continue;
            }

            if (array_key_exists('extra_price_try', $value)) {
                $extra += max(0.0, (float) $value['extra_price_try']);
            }
        }

        return $extra;
    }
}
