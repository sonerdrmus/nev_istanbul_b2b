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

        if (is_array($value) && self::isDeliverySelection($value)) {
            return self::formatDeliverySelection($value);
        }

        if (is_array($value) && $value !== [] && ! self::isLabelTypeSelection($value)) {
            $parts = [];
            foreach ($value as $item) {
                if (self::isLabelTypeSelection($item)) {
                    $parts[] = self::formatLabelTypeSelection($item);
                } elseif (is_scalar($item)) {
                    $label = trim((string) $item);
                    if ($label !== '') {
                        $parts[] = CatalogLabelTranslator::label($label);
                    }
                }
            }

            return $parts !== [] ? implode(' · ', $parts) : null;
        }

        if (self::isLabelTypeSelection($value)) {
            return self::formatLabelTypeSelection($value);
        }

        if (is_array($value)) {
            $parts = array_values(array_filter(array_map(
                static fn ($x) => is_scalar($x) ? CatalogLabelTranslator::label(trim((string) $x)) : '',
                $value
            )));

            return $parts !== [] ? implode(', ', $parts) : null;
        }

        if ($value === null || $value === '') {
            return null;
        }

        $text = trim((string) $value);

        return $text === '' ? null : CatalogLabelTranslator::label($text);
    }

    public static function formatLabelTypeSelection(array $value): string
    {
        $parts = [CatalogLabelTranslator::label(trim($value['option']))];

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

        if (! empty($value['description']) && is_string($value['description'])) {
            $desc = trim($value['description']);
            if ($desc !== '') {
                $parts[] = __('store.product.label_description_summary', ['text' => $desc]);
            }
        }

        return implode(' · ', array_filter($parts, static fn ($part) => $part !== ''));
    }

    /** @param  array<string, mixed>  $value */
    private static function isDeliverySelection(array $value): bool
    {
        return array_key_exists('sub_option', $value)
            || array_key_exists('estimated_delivery_time', $value)
            || array_key_exists('delivery_preset_id', $value);
    }

    /** @param  array<string, mixed>  $value */
    private static function formatDeliverySelection(array $value): string
    {
        $parts = [];
        $option = CatalogLabelTranslator::label(trim((string) ($value['option'] ?? '')));
        if ($option !== '') {
            $parts[] = $option;
        }
        $subOption = CatalogLabelTranslator::label(trim((string) ($value['sub_option'] ?? '')));
        if ($subOption !== '') {
            $parts[] = __('store.cart.detail_sub_option').': '.$subOption;
        }
        $estimate = CatalogLabelTranslator::label(trim((string) ($value['estimated_delivery_time'] ?? '')));
        if ($estimate !== '') {
            $parts[] = __('store.cart.detail_estimated_delivery').': '.$estimate;
        }
        $description = trim((string) ($value['sub_option_description'] ?? ''));
        if ($description !== '') {
            $parts[] = __('store.cart.detail_sub_option_description').': '.$description;
        }

        return implode(' · ', $parts);
    }
}
