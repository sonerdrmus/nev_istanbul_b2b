<?php

namespace App\Support;

use App\Filament\Resources\ProductResource;
use App\Models\InterfaceCertificateVariation;
use App\Models\InterfaceColorVariation;
use App\Models\InterfaceDeliveryMethodVariation;
use App\Models\InterfaceFabricTypeVariation;
use App\Models\InterfaceLabelTypeVariation;
use App\Models\InterfaceMoldModelVariation;
use App\Models\InterfacePackagingPreferenceVariation;
use App\Models\ProductVariationOption;
use App\Models\SizeTable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Schema;

class ProductVariationOptionInterfaceSync
{
    /** Varyasyon yönetimi → ürün varyasyon sekmesi eşlemesi. */
    public const VARIATION_TYPES = [
        'fabric',
        'color',
        'label_type',
        'packaging_type',
        'certificate_type',
        'mold_model_type',
        'delivery_type',
        'size_table',
    ];

    public static function appendMissingProductOptions(string $variationType, ?int $onlyPresetId = null): int
    {
        return ProductResource::appendMissingInterfacePresetOptions($variationType, $onlyPresetId);
    }

    /**
     * Kumaş–ürün atamalarını ürünlerin "Kumaş türü" varyasyon seçenekleriyle eşitler.
     *
     * @return array{added: int, removed: int}
     */
    public static function reconcileFabricProductOptions(?int $productId = null, ?int $presetId = null): array
    {
        return ProductResource::reconcileFabricOptionsForProducts($productId, $presetId);
    }

    /**
     * Tek preset kaydını bağlı ürün seçeneklerine yansıtır; pasifse bağlı seçenekleri kaldırır.
     */
    public static function syncPreset(object $preset, ?string $type = null): int
    {
        $type ??= static::variationTypeForPreset($preset);
        if ($type === null) {
            return 0;
        }

        if (! static::presetIsEligibleForProductOptions($type, $preset)) {
            return static::deleteProductOptionsForPreset($type, (int) $preset->getKey());
        }

        return match ($type) {
            'color' => static::fromColorPreset($preset),
            'fabric' => static::fromFabricPreset($preset),
            'label_type' => static::fromLabelPreset($preset),
            'packaging_type' => static::fromPackagingPreset($preset),
            'certificate_type' => static::fromCertificatePreset($preset),
            'mold_model_type' => static::fromMoldModelPreset($preset),
            'delivery_type' => static::fromDeliveryMethodPreset($preset),
            'size_table' => static::fromSizeTable($preset),
            default => 0,
        };
    }

    /**
     * Bir varyasyon tipi için: güncelle, eksik ekle, silinmiş/pasif preset bağlantılarını temizle.
     *
     * @return array{updated: int, added: int, removed: int}
     */
    public static function syncVariationType(string $variationType): array
    {
        if (! in_array($variationType, self::VARIATION_TYPES, true)) {
            return ['updated' => 0, 'added' => 0, 'removed' => 0];
        }

        $updated = 0;
        foreach (static::allPresetsForType($variationType) as $preset) {
            if (static::presetIsEligibleForProductOptions($variationType, $preset)) {
                $updated += static::syncPreset($preset, $variationType);
            }
        }

        $removed = static::removeOrphanedProductOptions($variationType)
            + static::removeInactivePresetProductOptions($variationType);

        if ($variationType === 'fabric') {
            $reconciled = static::reconcileFabricProductOptions();
            $added = $reconciled['added'];
            $removed += $reconciled['removed'];
        } else {
            $added = static::appendMissingProductOptions($variationType);
        }

        return [
            'updated' => $updated,
            'added' => $added,
            'removed' => $removed,
        ];
    }

    /**
     * Varyasyon yönetimindeki tüm preset tiplerini ürün varyasyonlarıyla eşitler.
     *
     * @return array<string, array{updated: int, added: int, removed: int}>
     */
    public static function syncAll(): array
    {
        $results = [];
        foreach (self::VARIATION_TYPES as $type) {
            $results[$type] = static::syncVariationType($type);
        }

        return $results;
    }

    public static function variationTypeForPreset(object $preset): ?string
    {
        return match ($preset::class) {
            InterfaceFabricTypeVariation::class => 'fabric',
            InterfaceColorVariation::class => 'color',
            InterfaceLabelTypeVariation::class => 'label_type',
            InterfacePackagingPreferenceVariation::class => 'packaging_type',
            InterfaceCertificateVariation::class => 'certificate_type',
            InterfaceMoldModelVariation::class => 'mold_model_type',
            InterfaceDeliveryMethodVariation::class => 'delivery_type',
            SizeTable::class => 'size_table',
            default => null,
        };
    }

    public static function presetIsEligibleForProductOptions(string $type, object $preset): bool
    {
        return match ($type) {
            'color' => (bool) ($preset->is_active ?? true) && filled($preset->image_path ?? null),
            'label_type', 'packaging_type', 'certificate_type', 'mold_model_type', 'delivery_type', 'fabric' => (bool) ($preset->is_active ?? true),
            'size_table' => true,
            default => false,
        };
    }

    public static function deleteProductOptionsForPreset(string $type, int $presetId): int
    {
        $fkField = ProductResource::interfacePresetForeignKeyForVariationType($type);
        if ($fkField === null) {
            return 0;
        }

        return ProductVariationOption::query()->where($fkField, $presetId)->delete();
    }

    public static function removeOrphanedProductOptions(string $variationType): int
    {
        $fkField = ProductResource::interfacePresetForeignKeyForVariationType($variationType);
        if ($fkField === null) {
            return 0;
        }

        $validIds = static::validPresetIdsForType($variationType);
        if ($validIds === []) {
            return ProductVariationOption::query()->whereNotNull($fkField)->delete();
        }

        return ProductVariationOption::query()
            ->whereNotNull($fkField)
            ->whereNotIn($fkField, $validIds)
            ->delete();
    }

    public static function removeInactivePresetProductOptions(string $variationType): int
    {
        if ($variationType === 'size_table') {
            return 0;
        }

        $fkField = ProductResource::interfacePresetForeignKeyForVariationType($variationType);
        if ($fkField === null) {
            return 0;
        }

        $inactiveIds = static::allPresetsForType($variationType)
            ->filter(fn (object $preset): bool => ! static::presetIsEligibleForProductOptions($variationType, $preset))
            ->pluck('id')
            ->map(fn ($id): int => (int) $id)
            ->all();

        if ($inactiveIds === []) {
            return 0;
        }

        return ProductVariationOption::query()->whereIn($fkField, $inactiveIds)->delete();
    }

    /** @return Collection<int, object> */
    public static function allPresetsForType(string $type): Collection
    {
        return match ($type) {
            'fabric' => InterfaceFabricTypeVariation::query()->orderBy('sort_order')->orderBy('id')->get(),
            'color' => InterfaceColorVariation::query()->orderBy('sort_order')->orderBy('id')->get(),
            'label_type' => InterfaceLabelTypeVariation::query()->orderBy('sort_order')->orderBy('id')->get(),
            'packaging_type' => InterfacePackagingPreferenceVariation::query()->orderBy('sort_order')->orderBy('id')->get(),
            'certificate_type' => InterfaceCertificateVariation::query()->orderBy('sort_order')->orderBy('id')->get(),
            'mold_model_type' => InterfaceMoldModelVariation::query()->orderBy('sort_order')->orderBy('id')->get(),
            'delivery_type' => InterfaceDeliveryMethodVariation::query()->orderBy('sort_order')->orderBy('id')->get(),
            'size_table' => SizeTable::query()->orderBy('sort_order')->orderBy('id')->get(),
            default => collect(),
        };
    }

    /** @return array<int, int> */
    public static function validPresetIdsForType(string $type): array
    {
        return static::allPresetsForType($type)
            ->pluck('id')
            ->map(fn ($id): int => (int) $id)
            ->all();
    }

    public static function fromColorPreset(InterfaceColorVariation $preset): int
    {
        if (! static::hasProductVariationOptionColumn('interface_color_variation_id')) {
            return 0;
        }

        return $preset->productVariationOptions()->update([
            'option_value' => static::displayName($preset->name, 'Renk #'.$preset->getKey()),
            'sort_order' => (int) ($preset->sort_order ?? 0),
            'option_image' => filled($preset->image_path) ? $preset->image_path : null,
            'option_color' => filled($preset->hex_color) ? (string) $preset->hex_color : null,
        ]);
    }

    public static function fromFabricPreset(InterfaceFabricTypeVariation $preset): int
    {
        if (! static::hasProductVariationOptionColumn('interface_fabric_type_variation_id')) {
            return 0;
        }

        return $preset->productVariationOptions()->update([
            'option_value' => static::displayName($preset->name, 'Kumaş #'.$preset->getKey()),
            'sort_order' => (int) ($preset->sort_order ?? 0),
            'option_image' => filled($preset->image_path) ? $preset->image_path : null,
            'price_delta' => ProductVariationOption::normalizePriceMultiplier($preset->price_multiplier),
        ]);
    }

    public static function fromLabelPreset(InterfaceLabelTypeVariation $preset): int
    {
        if (! static::hasProductVariationOptionColumn('interface_label_type_variation_id')) {
            return 0;
        }

        return static::syncLinkedOptions(
            $preset->productVariationOptions(),
            (string) $preset->name,
            $preset->image_path,
            (int) ($preset->sort_order ?? 0),
        );
    }

    public static function fromPackagingPreset(InterfacePackagingPreferenceVariation $preset): int
    {
        if (! static::hasProductVariationOptionColumn('interface_packaging_preference_variation_id')) {
            return 0;
        }

        return static::syncLinkedOptions(
            $preset->productVariationOptions(),
            (string) $preset->name,
            $preset->image_path,
            (int) ($preset->sort_order ?? 0),
        );
    }

    public static function fromCertificatePreset(InterfaceCertificateVariation $preset): int
    {
        if (! static::hasProductVariationOptionColumn('interface_certificate_variation_id')) {
            return 0;
        }

        return $preset->productVariationOptions()->update([
            'option_value' => (string) $preset->name,
            'info_text' => filled($preset->description) ? (string) $preset->description : null,
            'sort_order' => (int) ($preset->sort_order ?? 0),
            'option_image' => filled($preset->image_path) ? $preset->image_path : null,
            'price_delta' => ProductVariationOption::normalizePriceMultiplier($preset->price_multiplier),
        ]);
    }

    public static function fromMoldModelPreset(InterfaceMoldModelVariation $preset): int
    {
        if (! static::hasProductVariationOptionColumn('interface_mold_model_variation_id')) {
            return 0;
        }

        return $preset->productVariationOptions()->update([
            'option_value' => (string) $preset->name,
            'sort_order' => (int) ($preset->sort_order ?? 0),
            'option_image' => filled($preset->image_path) ? $preset->image_path : null,
            'price_delta' => ProductVariationOption::normalizePriceMultiplier($preset->price_multiplier),
        ]);
    }

    public static function fromDeliveryMethodPreset(InterfaceDeliveryMethodVariation $preset): int
    {
        if (! static::hasProductVariationOptionColumn('interface_delivery_method_variation_id')) {
            return 0;
        }

        return $preset->productVariationOptions()->update([
            'option_value' => (string) $preset->name,
            'info_text' => filled($preset->description) ? (string) $preset->description : null,
            'sort_order' => (int) ($preset->sort_order ?? 0),
            'option_image' => filled($preset->image_path) ? $preset->image_path : null,
            'price_delta' => ProductVariationOption::normalizePriceMultiplier($preset->price_multiplier),
        ]);
    }

    public static function fromSizeTable(SizeTable $table): int
    {
        $label = trim((string) ($table->title ?: $table->name ?: ''));
        if ($label === '') {
            $label = (string) $table->slug;
        }

        return ProductVariationOption::query()
            ->where('size_table_id', $table->getKey())
            ->update([
                'option_value' => $label,
                'sort_order' => (int) ($table->sort_order ?? 0),
            ]);
    }

    /**
     * @param  HasMany<ProductVariationOption, \Illuminate\Database\Eloquent\Model>  $relation
     */
    private static function syncLinkedOptions(
        HasMany $relation,
        string $optionValue,
        ?string $imagePath,
        int $sortOrder,
    ): int {
        return $relation->update([
            'option_value' => $optionValue,
            'sort_order' => $sortOrder,
            'option_image' => filled($imagePath) ? $imagePath : null,
        ]);
    }

    private static function displayName(?string $name, string $fallback): string
    {
        $label = trim((string) ($name ?? ''));

        return $label !== '' ? $label : $fallback;
    }

    private static function hasProductVariationOptionColumn(string $column): bool
    {
        return Schema::hasTable('product_variation_options')
            && Schema::hasColumn('product_variation_options', $column);
    }
}
