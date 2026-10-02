<?php

namespace App\Models;

use App\Models\Concerns\FillsLocalizedNameFromCatalog;
use App\Support\LocaleContent;
use App\Support\PackagingTypeVariationDisplay;
use Illuminate\Database\Eloquent\Model;

class ProductVariationOption extends Model
{
    use FillsLocalizedNameFromCatalog;

    protected $fillable = [
        'product_variation_id',
        'interface_color_variation_id',
        'interface_fabric_type_variation_id',
        'interface_label_type_variation_id',
        'interface_packaging_preference_variation_id',
        'interface_certificate_variation_id',
        'interface_delivery_method_variation_id',
        'interface_mold_model_variation_id',
        'size_table_id',
        'option_value',
        'option_value_en',
        'option_value_it',
        'info_text',
        'info_text_en',
        'info_text_it',
        'option_color',
        'option_image',
        'option_image_size',
        'price_delta',
        'stock_quantity',
        'parent_option_id',
        'parent_option_ids',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'price_delta' => 'decimal:3',
            'parent_option_ids' => 'array',
        ];
    }

    protected static function localizedNameSourceAttribute(): string
    {
        return 'option_value';
    }

    public function getDisplayValueAttribute(): string
    {
        return LocaleContent::display($this->option_value, $this->option_value_en, $this->option_value_it);
    }

    public function getLocalizedInfoTextAttribute(): string
    {
        return LocaleContent::display($this->info_text, $this->info_text_en, $this->info_text_it);
    }

    /**
     * Option-level EN/IT wins. Empty locale fields fall back to the linked preset translation.
     */
    public function storeInfoText(?string $presetTr, ?string $presetEn = null, ?string $presetIt = null): string
    {
        return LocaleContent::display(
            filled($this->info_text) ? $this->info_text : $presetTr,
            filled($this->info_text_en) ? $this->info_text_en : $presetEn,
            filled($this->info_text_it) ? $this->info_text_it : $presetIt,
        );
    }

    protected static function booted(): void
    {
        static::saving(function (ProductVariationOption $option): void {
            if (! empty($option->size_table_id)) {
                $option->syncOptionValueFromSizeTable();
            }
        });
    }

    public function syncOptionValueFromSizeTable(): void
    {
        if (empty($this->size_table_id)) {
            return;
        }

        $table = $this->relationLoaded('sizeTable')
            ? $this->sizeTable
            : SizeTable::query()->find($this->size_table_id);

        if (! $table) {
            return;
        }

        $label = trim((string) ($table->title ?: $table->name ?: ''));
        $this->option_value = $label !== '' ? $label : (string) $table->slug;
    }

    /** Seçenek hangi üst seçenek(ler)e bağlı – hem tek parent_option_id hem parent_option_ids desteklenir. */
    public function getParentOptionIdsList(): array
    {
        $ids = $this->parent_option_ids ?? [];
        if (is_array($ids)) {
            $ids = array_filter(array_map('intval', $ids));
        } else {
            $ids = [];
        }
        if ($this->parent_option_id && ! in_array((int) $this->parent_option_id, $ids, true)) {
            $ids[] = (int) $this->parent_option_id;
        }

        return $ids;
    }

    public function variation()
    {
        return $this->belongsTo(ProductVariation::class, 'product_variation_id');
    }

    public function interfaceColorVariation()
    {
        return $this->belongsTo(InterfaceColorVariation::class, 'interface_color_variation_id');
    }

    public function interfaceFabricTypeVariation()
    {
        return $this->belongsTo(InterfaceFabricTypeVariation::class, 'interface_fabric_type_variation_id');
    }

    public function interfaceLabelTypeVariation()
    {
        return $this->belongsTo(InterfaceLabelTypeVariation::class, 'interface_label_type_variation_id');
    }

    public function interfacePackagingPreferenceVariation()
    {
        return $this->belongsTo(InterfacePackagingPreferenceVariation::class, 'interface_packaging_preference_variation_id');
    }

    public function interfaceCertificateVariation()
    {
        return $this->belongsTo(InterfaceCertificateVariation::class, 'interface_certificate_variation_id');
    }

    public function interfaceDeliveryMethodVariation()
    {
        return $this->belongsTo(InterfaceDeliveryMethodVariation::class, 'interface_delivery_method_variation_id');
    }

    public function interfaceMoldModelVariation()
    {
        return $this->belongsTo(InterfaceMoldModelVariation::class, 'interface_mold_model_variation_id');
    }

    public function sizeTable()
    {
        return $this->belongsTo(SizeTable::class);
    }

    public function parentOption()
    {
        return $this->belongsTo(ProductVariationOption::class, 'parent_option_id');
    }

    public function childOptions()
    {
        return $this->hasMany(ProductVariationOption::class, 'parent_option_id');
    }

    /** 0 veya geçersiz değer = fiyatı değiştirmez (×1). */
    public static function normalizePriceMultiplier(mixed $raw): float
    {
        $f = (float) $raw;

        return $f > 0.0 ? $f : 1.0;
    }

    /**
     * Seçilen her seçenek için `price_delta` alanını çarpan olarak çarpımına çevirir (ör. 1,5 × 2 = 3).
     */
    public static function combinedMultiplierForSelections(Product $product, array $selections): float
    {
        $product->loadMissing('variations.options');

        $factor = 1.0;

        foreach ($selections as $variationName => $optionValue) {
            if (in_array((string) $variationName, [
                'size_quantities',
                'product_customization',
                'product_customization_notes',
                'product_customization_table',
                'quick_order',
            ], true)) {
                continue;
            }

            $variation = $product->variations->first(
                fn ($row) => trim((string) $row->name) === trim((string) $variationName)
            );
            if (! $variation) {
                continue;
            }

            $factor *= self::multiplierForStoredValue($variation, $optionValue);
        }

        return $factor;
    }

    private static function multiplierForStoredValue(ProductVariation $variation, mixed $optionValue): float
    {
        if (is_array($optionValue) && ! self::isMultiValueList($optionValue)) {
            return self::multiplierForStructuredChoice($variation, $optionValue);
        }

        if (is_array($optionValue)) {
            $factor = 1.0;
            foreach ($optionValue as $value) {
                if (is_array($value)) {
                    $factor *= self::multiplierForStructuredChoice($variation, $value);

                    continue;
                }
                if ($value === null || trim((string) $value) === '') {
                    continue;
                }
                $factor *= self::multiplierForOptionLabel($variation, (string) $value);
            }

            return $factor;
        }

        return self::multiplierForOptionLabel($variation, (string) $optionValue);
    }

    /** @param  array<string, mixed>  $value */
    private static function multiplierForStructuredChoice(ProductVariation $variation, array $value): float
    {
        $factor = self::multiplierForOptionLabel($variation, self::resolveSelectionOptionLabel($value));
        if (isset($value['sub_option_multiplier']) && is_numeric($value['sub_option_multiplier'])) {
            $sub = (float) $value['sub_option_multiplier'];
            if ($sub > 0.0) {
                $factor *= $sub;
            }
        }

        return $factor;
    }

    private static function multiplierForOptionLabel(ProductVariation $variation, string $label): float
    {
        $option = self::findOptionByStoredLabel($variation, $label);

        return $option ? self::normalizePriceMultiplier($option->price_delta) : 1.0;
    }

    /**
     * Sepet, seçimi bazen görünen adla kaydeder (EN/IT veya "etiket · adet").
     * Çarpan, kanonik option_value ile aynı seçeneğe bağlanır.
     */
    private static function findOptionByStoredLabel(ProductVariation $variation, string $label): ?self
    {
        $needle = trim($label);
        if ($needle === '') {
            return null;
        }

        $candidates = [$needle];
        $head = trim((string) preg_split('/\s+·\s+/u', $needle)[0]);
        if ($head !== '' && $head !== $needle) {
            $candidates[] = $head;
        }

        foreach (['option_value', 'option_value_en', 'option_value_it'] as $field) {
            foreach ($candidates as $candidate) {
                $option = $variation->options->first(
                    fn ($row) => trim((string) $row->{$field}) === $candidate
                );
                if ($option) {
                    return $option;
                }
            }
        }

        return null;
    }

    /** @param  array<string, mixed>  $selections */
    public static function additiveExtraTryForSelections(array $selections): float
    {
        return PackagingTypeVariationDisplay::additiveExtraTryFromSelections($selections);
    }

    private static function resolveSelectionOptionLabel(mixed $optionValue): string
    {
        if (is_array($optionValue) && array_key_exists('option', $optionValue)) {
            return self::resolveSelectionOptionLabel($optionValue['option']);
        }

        if (is_array($optionValue)) {
            return '';
        }

        return trim((string) $optionValue);
    }

    /** @param  array<mixed>  $value */
    private static function isMultiValueList(array $value): bool
    {
        if (array_key_exists('option', $value)) {
            return false;
        }

        return array_is_list($value);
    }

    /**
     * Seçilen varyasyonlara göre çarpan bilgisi (TL farkı için `delta_total` artık kullanılmaz).
     *
     * @return array{multiplier_total: float, delta_total: float, breakdown: array<string, array<string, float>>}
     */
    public static function forSelection(Product $product, array $selections): array
    {
        $factor = self::combinedMultiplierForSelections($product, $selections);

        return [
            'multiplier_total' => $factor,
            'delta_total' => 0.0,
            'breakdown' => [],
        ];
    }
}
