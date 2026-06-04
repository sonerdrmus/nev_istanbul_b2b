<?php

namespace App\Support;

class LabelTypeVariationDisplay
{
    public static function isLabelTypeSelection(mixed $value): bool
    {
        return is_array($value) && array_key_exists('option', $value) && is_string($value['option']);
    }

    public static function formatVariationValue(mixed $value): ?string
    {
        if (PackagingTypeVariationDisplay::isPackagingTypeSelection($value)) {
            return PackagingTypeVariationDisplay::formatPackagingSelection($value);
        }

        if (self::isLabelTypeSelection($value)) {
            return self::formatLabelTypeSelection($value);
        }

        if (is_array($value)) {
            $parts = array_values(array_filter(array_map(
                static fn ($x) => is_scalar($x) ? trim((string) $x) : '',
                $value
            )));

            return $parts !== [] ? implode(', ', $parts) : null;
        }

        if ($value === null || $value === '') {
            return null;
        }

        return (string) $value;
    }

    public static function formatLabelTypeSelection(array $value): string
    {
        $parts = [trim($value['option'])];

        if (array_key_exists('custom_print', $value) && $value['custom_print'] !== null) {
            $parts[] = $value['custom_print']
                ? __('store.product.label_custom_print_summary_yes')
                : __('store.product.label_custom_print_summary_no');
        }

        if (! empty($value['custom_print_artwork']) && ($value['custom_print'] ?? false)) {
            $artworkKey = (string) $value['custom_print_artwork'];
            if ($artworkKey === 'customer_send') {
                $parts[] = __('store.product.label_custom_print_artwork_summary_customer');
            } elseif ($artworkKey === 'company_prepare') {
                $parts[] = __('store.product.label_custom_print_artwork_summary_company');
            }
        }

        if (! empty($value['positions']) && is_array($value['positions'])) {
            $posLabels = [];
            foreach ($value['positions'] as $position) {
                if ($position === 'front') {
                    $posLabels[] = __('store.product.label_position_front');
                } elseif ($position === 'back') {
                    $posLabels[] = __('store.product.label_position_back');
                }
            }

            if ($posLabels !== []) {
                $parts[] = __('store.product.label_position_summary', [
                    'positions' => implode(', ', $posLabels),
                ]);
            }
        }

        return implode(' · ', array_filter($parts, static fn ($part) => $part !== ''));
    }
}
