<?php

namespace App\Models;

use App\Support\CatalogLabelTranslator;
use Illuminate\Database\Eloquent\Model;

class ProductVariationOption extends Model
{
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
        'info_text',
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

    public function getDisplayValueAttribute(): string
    {
        return CatalogLabelTranslator::label($this->option_value);
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
            if ((string) $variationName === 'size_quantities') {
                continue;
            }

            $variation = $product->variations->firstWhere('name', (string) $variationName);
            if (! $variation) {
                continue;
            }

            $optionLabel = self::resolveSelectionOptionLabel($optionValue);

            if (is_array($optionValue) && ! self::isMultiValueList($optionValue)) {
                $option = $variation->options->firstWhere('option_value', $optionLabel);
                if ($option) {
                    $factor *= self::normalizePriceMultiplier($option->price_delta);
                }

                continue;
            }

            if (is_array($optionValue)) {
                foreach ($optionValue as $v) {
                    if ($v === null || trim((string) $v) === '') {
                        continue;
                    }
                    $option = $variation->options->firstWhere('option_value', (string) $v);
                    if ($option) {
                        $factor *= self::normalizePriceMultiplier($option->price_delta);
                    }
                }
            } else {
                $option = $variation->options->firstWhere('option_value', $optionLabel);
                if ($option) {
                    $factor *= self::normalizePriceMultiplier($option->price_delta);
                }
            }
        }

        return $factor;
    }

    /** @param  array<string, mixed>  $selections */
    public static function additiveExtraTryForSelections(array $selections): float
    {
        return PackagingTypeVariationDisplay::additiveExtraTryFromSelections($selections);
    }

    private static function resolveSelectionOptionLabel(mixed $optionValue): string
    {
        if (is_array($optionValue) && array_key_exists('option', $optionValue)) {
            return trim((string) $optionValue['option']);
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
