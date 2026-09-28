<?php

namespace App\Support;

class VariationSelectionDisplay
{
    /** @var list<string> */
    private const SKIP_KEYS = [
        'extra_price_try',
        'price_multiplier',
        'delivery_preset_id',
        'sub_option_id',
        'sub_option_multiplier',
        'packaging_slug',
        'material_slug',
        'sticker_design',
    ];

    /** @var list<string> */
    private const FREE_TEXT_KEYS = [
        'description',
        'sub_option_description',
        'customization_label',
        'sticker_design_label',
    ];

    /**
     * @return list<array{label: string, value: string, divider?: bool}>
     */
    public static function rows(mixed $value): array
    {
        if (! is_array($value)) {
            $formatted = self::formatScalar($value, '');

            return $formatted === '' ? [] : [['label' => '', 'value' => $formatted]];
        }

        if (array_is_list($value)) {
            $rows = [];
            foreach ($value as $child) {
                $childRows = self::rows($child);
                if ($rows !== [] && $childRows !== []) {
                    $rows[] = ['label' => '', 'value' => '', 'divider' => true];
                }
                foreach ($childRows as $row) {
                    $rows[] = $row;
                }
            }

            return $rows;
        }

        $rows = [];
        foreach ($value as $key => $child) {
            $key = (string) $key;
            if (in_array($key, self::SKIP_KEYS, true)) {
                continue;
            }
            if ($key === 'positions') {
                $formatted = self::formatScalar($child, $key);
                if ($formatted !== '') {
                    $rows[] = ['label' => self::label($key), 'value' => $formatted];
                }

                continue;
            }
            if (is_array($child)) {
                foreach (self::rows($child) as $row) {
                    $rows[] = $row;
                }

                continue;
            }
            $formatted = self::formatScalar($child, $key);
            if ($formatted !== '') {
                $rows[] = ['label' => self::label($key), 'value' => $formatted];
            }
        }

        return $rows;
    }

    public static function panelHtml(mixed $data): string
    {
        $previous = app()->getLocale();
        app()->setLocale('tr');

        try {
            if (! is_array($data) || $data === []) {
                return '<span style="color:#64748b;">—</span>';
            }

            $blocks = [];
            foreach ($data as $name => $value) {
                $html = self::panelBlock((string) $name, $value);
                if ($html !== '') {
                    $blocks[] = $html;
                }
            }

            if ($blocks === []) {
                return '<span style="color:#64748b;">—</span>';
            }

            return '<div style="display:flex;flex-direction:column;gap:12px;text-align:left;">'.implode('', $blocks).'</div>';
        } finally {
            app()->setLocale($previous);
        }
    }

    private static function panelBlock(string $name, mixed $value): string
    {
        if ($name === 'quick_order' && is_array($value)) {
            $rows = [];
            $notes = trim((string) ($value['notes'] ?? ''));
            if ($notes !== '') {
                $rows[] = ['label' => 'Not', 'value' => $notes];
            }
            $imageUrl = trim((string) ($value['image_url'] ?? ''));
            if ($imageUrl !== '') {
                $rows[] = ['label' => 'Görsel', 'value' => 'Görseli aç', 'href' => $imageUrl];
            }
            if ($rows === []) {
                return '';
            }

            return self::panelCard(__('store.product.quick_order_summary'), $rows);
        }

        if ($name === 'product_customization') {
            if ($value === 'skipped') {
                return self::panelCard(__('store.product.customization_summary_section_label'), [
                    ['label' => '', 'value' => __('store.product.skip_customization')],
                ]);
            }

            return '';
        }

        if ($name === 'product_customization_notes') {
            $notes = is_string($value) ? trim($value) : '';
            if ($notes === '') {
                return '';
            }

            return self::panelCard(__('store.product.customization_panel_title'), [
                ['label' => '', 'value' => $notes],
            ]);
        }

        if ($name === 'size_quantities' && is_array($value)) {
            $rows = [];
            foreach ($value as $size => $qty) {
                if ((int) $qty > 0) {
                    $rows[] = ['label' => (string) $size, 'value' => (int) $qty.' adet'];
                }
            }
            if ($rows === []) {
                return '';
            }

            return self::panelCard(__('store.order_confirmation.size_breakdown'), $rows);
        }

        if ($name === 'product_customization_table' && is_array($value)) {
            return self::customizationTableCard($value);
        }

        if (is_array($value) && array_is_list($value) && count($value) > 1) {
            $sections = '';
            $index = 1;
            foreach ($value as $child) {
                $rows = self::rows($child);
                if ($rows === []) {
                    continue;
                }
                $sections .= '<p style="margin:10px 0 4px;font-size:11px;font-weight:700;letter-spacing:.04em;text-transform:uppercase;color:#64748b;">Seçim '.$index.'</p>';
                $sections .= self::rowsHtml($rows);
                $index++;
            }
            if ($sections === '') {
                return '';
            }

            return self::panelShell(CatalogLabelTranslator::label($name, 'tr'), $sections);
        }

        $rows = self::rows($value);
        if ($rows === []) {
            return '';
        }

        return self::panelCard(CatalogLabelTranslator::label($name, 'tr'), $rows);
    }

    /** @param  array<string, mixed>  $value */
    private static function customizationTableCard(array $value): string
    {
        $custRows = $value['rows'] ?? (isset($value['row_id']) ? [$value] : []);
        $blocks = '';
        $index = 1;
        foreach ($custRows as $row) {
            if (! is_array($row)) {
                continue;
            }
            $position = trim((string) ($row['konum_label'] ?? ''));
            if ($position === '') {
                $position = CatalogLabelTranslator::label((string) ($row['konum'] ?? ''), 'tr');
            }
            $fields = array_values(array_filter([
                ['label' => 'Konum', 'value' => $position],
                ['label' => 'Ölçü', 'value' => trim((string) ($row['en_boy_cm'] ?? ''))],
                ['label' => 'Alan', 'value' => trim((string) ($row['alan_cm2_display'] ?? ($row['alan_m2_display'] ?? '')))],
                ['label' => 'Ebat', 'value' => trim((string) ($row['ebat'] ?? ''))],
                ['label' => 'Renk sayısı', 'value' => trim((string) ($row['renk_sayisi'] ?? ''))],
                ['label' => 'Baskı tekniği', 'value' => CatalogLabelTranslator::label((string) ($row['baski_teknigi'] ?? ''), 'tr')],
            ], static fn (array $field): bool => $field['value'] !== ''));
            if ($fields === []) {
                continue;
            }
            if (count($custRows) > 1) {
                $blocks .= '<p style="margin:10px 0 4px;font-size:11px;font-weight:700;letter-spacing:.04em;text-transform:uppercase;color:#64748b;">Baskı '.$index.'</p>';
            }
            $blocks .= self::rowsHtml($fields);
            $index++;
        }
        if ($blocks === '') {
            return '';
        }

        return self::panelShell(__('store.product.customization_summary_section_label'), $blocks);
    }

    /**
     * @param  list<array{label?: string, value?: string, divider?: bool, href?: string}>  $rows
     */
    private static function panelCard(string $title, array $rows): string
    {
        return self::panelShell($title, self::rowsHtml($rows));
    }

    private static function panelShell(string $title, string $body): string
    {
        return '<section style="overflow:hidden;border:1px solid #e2e8f0;border-radius:12px;background:#fff;">'
            .'<h4 style="margin:0;padding:8px 14px;border-bottom:1px solid #e2e8f0;background:#f8fafc;font-size:12px;font-weight:700;letter-spacing:.04em;text-transform:uppercase;color:#334155;">'.e($title).'</h4>'
            .'<div style="padding:4px 14px 8px;">'.$body.'</div></section>';
    }

    /**
     * @param  list<array{label?: string, value?: string, divider?: bool, href?: string}>  $rows
     */
    private static function rowsHtml(array $rows): string
    {
        $items = '';
        foreach ($rows as $row) {
            if (! empty($row['divider'])) {
                $items .= '<div style="margin:6px 0;border-top:1px solid #e2e8f0;"></div>';

                continue;
            }
            $value = (string) ($row['value'] ?? '');
            if ($value === '') {
                continue;
            }
            $valueHtml = e($value);
            if (! empty($row['href'])) {
                $valueHtml = '<a href="'.e((string) $row['href']).'" target="_blank" rel="noopener" style="color:#1d4ed8;text-decoration:underline;">'.$valueHtml.'</a>';
            }
            $label = (string) ($row['label'] ?? '');
            $labelHtml = $label !== ''
                ? '<div style="font-size:12px;font-weight:600;color:#64748b;">'.e($label).'</div>'
                : '';
            $items .= '<div style="padding:8px 0;border-bottom:1px solid #f1f5f9;">'.$labelHtml
                .'<div style="margin-top:2px;font-size:14px;line-height:1.45;font-weight:600;color:#0f172a;white-space:pre-wrap;word-break:break-word;">'.$valueHtml.'</div></div>';
        }

        return $items;
    }

    private static function label(string $key): string
    {
        return match ($key) {
            'option' => __('store.cart.detail_option'),
            'custom_print' => __('store.cart.detail_custom_print'),
            'custom_print_artwork' => __('store.cart.detail_custom_print_artwork'),
            'positions' => __('store.cart.detail_positions'),
            'description' => __('store.cart.detail_description'),
            'material' => __('store.cart.detail_material'),
            'customization_label', 'customization' => __('store.cart.detail_customization'),
            'barcode_area' => __('store.cart.detail_barcode_area'),
            'sticker_design_label' => __('store.cart.detail_sticker_design'),
            'sub_option' => __('store.cart.detail_sub_option'),
            'sub_option_description' => __('store.cart.detail_sub_option_description'),
            'estimated_delivery_time' => __('store.cart.detail_estimated_delivery'),
            default => CatalogLabelTranslator::label($key),
        };
    }

    private static function formatScalar(mixed $value, string $key): string
    {
        if (is_array($value)) {
            if ($key === 'positions') {
                $labels = [];
                foreach ($value as $position) {
                    $labels[] = match ((string) $position) {
                        'front' => __('store.product.label_position_front'),
                        'back' => __('store.product.label_position_back'),
                        default => (string) $position,
                    };
                }

                return implode(', ', array_filter($labels, static fn ($label) => $label !== ''));
            }

            return '';
        }

        if (is_bool($value)) {
            return $value ? __('store.cart.detail_yes') : __('store.cart.detail_no');
        }

        if ($value === null) {
            return '';
        }

        $text = trim((string) $value);
        if ($text === '') {
            return '';
        }

        if ($key === 'custom_print_artwork') {
            return match ($text) {
                'customer_send' => __('store.product.label_custom_print_artwork_summary_customer'),
                'company_prepare' => __('store.product.label_custom_print_artwork_summary_company'),
                default => $text,
            };
        }

        if (in_array($key, self::FREE_TEXT_KEYS, true)) {
            return $text;
        }

        return CatalogLabelTranslator::label($text);
    }
}
