<?php

namespace App\Support;

use App\Filament\Resources\ProductResource;
use App\Models\Product;
use App\Models\ProductVariation;
use Filament\Notifications\Notification;

/**
 * Admin'de varyasyon / seçenek kaydı sonrası çalışan senkronizasyonlar.
 *
 * Eskiden bunlar ürün formunun kaydında (EditProduct::afterSave, finalizeVariationOptionsInProductFormData)
 * tüm varyasyonlar için birlikte çalışıyordu. Varyasyonlar ve seçenekler artık ayrı ekranlarda
 * kaydedildiği için aynı kurallar yalnızca değişen varyasyon için uygulanır.
 */
final class ProductVariationAdminSync
{
    /** Kütüphaneden (preset) beslenen varyasyon tipleri. */
    public const PRESET_TYPES = ['fabric', 'color', 'label_type', 'certificate_type', 'mold_model_type', 'delivery_type', 'packaging_type', 'size_table'];

    public static function isPresetType(?string $type): bool
    {
        return in_array((string) $type, self::PRESET_TYPES, true);
    }

    /**
     * @param  bool  $fillPresetsIfEmpty  Preset tipinde hiç seçenek yoksa kütüphaneden doldur
     *                                    (eski ensureInterfacePresetOptionsInProductFormData davranışı).
     */
    public static function afterVariationSaved(ProductVariation $variation, bool $fillPresetsIfEmpty = true): void
    {
        $product = $variation->product;
        if (! $product instanceof Product) {
            return;
        }

        $type = (string) $variation->type;

        if (ProductResource::variationTypeRequiresSingleSelectOnly($type) && $variation->allows_multiple) {
            $variation->forceFill(['allows_multiple' => false])->saveQuietly();
        }

        $variation->load(['options' => fn ($query) => $query->orderBy('sort_order')->orderBy('id')]);

        if ($fillPresetsIfEmpty && $variation->options->isEmpty()) {
            self::createPresetOptions($variation);
        }

        // Seçenek görseli / preset alanlarını normalize et (eski syncVariationOptionImagesAfterFilamentSave).
        ProductResource::syncVariationOptionImagesAfterFilamentSave($product, [
            'variations' => [[
                'id' => $variation->getKey(),
                'type' => $type,
                'options' => $variation->options
                    ->map(fn ($option): array => $option->attributesToArray())
                    ->all(),
            ]],
        ]);

        // Etiket türü çarpanı kütüphaneden gelir (ürün bazlı değildir).
        if ($type === 'label_type') {
            ProductVariationOptionInterfaceSync::applyLabelPresetPrices($variation->options()->get());
        }

        self::afterVariationsChanged($product);
    }

    /**
     * Varyasyon tipi preset bir tipe değiştirildiğinde seçenekleri kütüphaneden yeniden oluşturur
     * (eski formda tip değişince seçenek satırları kütüphane kayıtlarıyla değiştiriliyordu).
     */
    public static function replaceOptionsWithPresets(ProductVariation $variation): void
    {
        $rows = ProductResource::interfacePresetOptionRowsForType((string) $variation->type, (int) $variation->product_id);
        if ($rows === null) {
            return;
        }

        $variation->options()->delete();
        $variation->unsetRelation('options');
        self::createPresetOptions($variation, $rows);

        if ($rows === []) {
            Notification::make()
                ->warning()
                ->title('Bu tip için kütüphanede kayıt bulunamadı')
                ->body('Varyasyon yönetimi bölümünden ilgili kayıtları ekleyin (gerekirse «Ürünler» alanından bu ürünü seçin).')
                ->send();
        }
    }

    /** @param  array<int, array<string, mixed>>|null  $rows */
    private static function createPresetOptions(ProductVariation $variation, ?array $rows = null): void
    {
        $type = (string) $variation->type;
        $rows ??= ProductResource::interfacePresetOptionRowsForType($type, (int) $variation->product_id);
        if (! is_array($rows) || $rows === []) {
            return;
        }

        foreach (array_values($rows) as $index => $row) {
            if (! isset($row['sort_order'])) {
                $row['sort_order'] = $index * 10;
            }
            ProductResource::createVariationOptionFromPresetRow($variation, $row, $type);
        }

        $variation->load(['options' => fn ($query) => $query->orderBy('sort_order')->orderBy('id')]);
    }

    public static function afterVariationsChanged(Product $product): void
    {
        ProductResource::syncPackagingAssignmentsFromProduct($product);
        ProductVariationOptionInterfaceSync::reconcilePackagingProductOptions((int) $product->getKey());
    }
}
