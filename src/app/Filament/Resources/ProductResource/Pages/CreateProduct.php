<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use App\Models\Product;
use App\Models\ProductVariationOptionPrice;
use Filament\Resources\Pages\CreateRecord;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    public static function getNavigationLabel(): string
    {
        return 'Yeni Ürün Ekle';
    }

    protected ?string $maxContentWidth = 'full';

    /** Sync için mutasyondan önceki ham varyasyon state'i (fiyatlar dahil). */
    private array $variationsStateForSync = [];

    /**
     * Sync için formun canlı state'ini (fiyatlar dahil) sakla.
     * options_by_parent dönüşümü model saving'de.
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $raw = (method_exists($this->form, 'getRawState') ? $this->form->getRawState() : $this->form->getState())['variations'] ?? [];
        $this->variationsStateForSync = is_array($raw) ? json_decode(json_encode($raw), true) : [];
        return $data;
    }

    protected function afterCreate(): void
    {
        /** @var Product $product */
        $product = $this->record;
        $variations = ! empty($this->variationsStateForSync)
            ? $this->variationsStateForSync
            : (method_exists($this->form, 'getRawState')
                ? ($this->form->getRawState()['variations'] ?? [])
                : ($this->form->getState()['variations'] ?? []));
        $this->syncVariationOptionPrices($product, $variations);
        $this->syncOptionMeta($product, $variations);
        $this->variationsStateForSync = [];
    }

    private function syncVariationOptionPrices(Product $product, array $variationsState): void
    {
        $product->load('variations');
        foreach ($variationsState as $vState) {
            $name = $vState['name'] ?? null;
            if (! $name) {
                continue;
            }
            $variation = $product->variations->firstWhere('name', $name);
            if (! $variation) {
                continue;
            }

            $optionData = [];
            foreach (($vState['options_with_prices'] ?? []) as $row) {
                $val = $row['option_value'] ?? null;
                if (! $val) continue;
                $stock = isset($row['stock_quantity']) && $row['stock_quantity'] !== '' && $row['stock_quantity'] !== null ? (int) $row['stock_quantity'] : null;
                $optionData[(string) $val] = ['price_delta_try' => (float) ($row['price_delta_try'] ?? 0), 'stock_quantity' => $stock];
            }
            $obp = $vState['options_by_parent'] ?? [];
            if (is_array($obp)) {
                $isListOfRows = array_is_list($obp) && isset($obp[0]) && is_array($obp[0]) && array_key_exists('options', $obp[0]);
                if ($isListOfRows) {
                    foreach ($obp as $parentRow) {
                        foreach (($parentRow['options'] ?? []) as $row) {
                            $val = $row['option_value'] ?? null;
                            if (! $val) continue;
                            $stock = isset($row['stock_quantity']) && $row['stock_quantity'] !== '' && $row['stock_quantity'] !== null ? (int) $row['stock_quantity'] : null;
                            $optionData[(string) $val] = ['price_delta_try' => (float) ($row['price_delta_try'] ?? 0), 'stock_quantity' => $stock];
                        }
                    }
                }
            }

            foreach ($optionData as $val => $data) {
                ProductVariationOptionPrice::updateOrCreate(
                    ['product_variation_id' => $variation->id, 'option_value' => $val],
                    ['price_delta_try' => $data['price_delta_try'], 'stock_quantity' => $data['stock_quantity']]
                );
            }
        }
    }

    private function syncOptionMeta(Product $product, array $formVariations): void
    {
        $product->load('variations');
        foreach ($formVariations as $vState) {
            $name = $vState['name'] ?? null;
            if (! $name) {
                continue;
            }
            $variation = $product->variations->firstWhere('name', $name);
            if (! $variation) {
                continue;
            }
            $meta = [];
            $collectMeta = function (array $row) use (&$meta): void {
                $val = trim((string) ($row['option_value'] ?? ''));
                if ($val === '') {
                    return;
                }
                $color = isset($row['option_color']) && (string) $row['option_color'] !== '' ? trim((string) $row['option_color']) : null;
                $image = isset($row['option_image']) && (string) $row['option_image'] !== '' ? trim((string) $row['option_image']) : null;
                if ($color !== null || $image !== null) {
                    $meta[$val] = array_filter(['color' => $color, 'image' => $image]);
                }
            };
            foreach ($vState['options_with_prices'] ?? [] as $row) {
                $collectMeta(is_array($row) ? $row : []);
            }
            foreach ($vState['options_by_parent'] ?? [] as $parentRow) {
                foreach ($parentRow['options'] ?? [] as $row) {
                    $collectMeta(is_array($row) ? $row : []);
                }
            }
            $variation->update(['option_meta' => $meta]);
        }
    }
}
