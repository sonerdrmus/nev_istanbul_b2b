<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use App\Models\Product;
use App\Models\ProductVariation;
use App\Models\ProductVariationOptionPrice;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    protected ?string $maxContentWidth = 'full';

    /** Sadece options_with_prices (bağımsız) için; bağlı modelden okunur. */
    private array $formVariations = [];

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->formVariations = is_array($data['variations'] ?? null) ? json_decode(json_encode($data['variations']), true) : [];
        return $data;
    }

    /** Ana kayıt güncellenir, ardından relationship repeater (variations) saveRelationships ile kaydedilir. */
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->update($data);

        $this->form->model($record)->saveRelationships();

        return $record;
    }

    protected function afterSave(): void
    {
        /** @var Product $product */
        $product = $this->record;
        $product->load('variations');
        $this->syncVariationOptionPrices($product);
        $this->syncOptionMeta($product, $this->formVariations);
        $this->formVariations = [];

        // Formda güncel fiyatların görünmesi için mevcut form state'teki fiyatları DB'den güncelle
        $this->refreshVariationPricesInFormState();
    }

    /** Kayıttan sonra form state'teki fiyatları DB'deki güncel değerlerle günceller. Yeni dizi atanır ki Livewire değişikliği kesin algılasın. */
    private function refreshVariationPricesInFormState(): void
    {
        $current = $this->data['variations'] ?? null;
        if (! is_array($current)) {
            return;
        }

        // Sync az önce yazdığı için DB'den taze oku (relation cache bypass)
        $optionRowsByVariationId = ProductVariationOptionPrice::query()
            ->whereIn('product_variation_id', $this->record->variations->pluck('id'))
            ->get()
            ->groupBy('product_variation_id');
        $priceMapsByVariationId = $optionRowsByVariationId->map(fn ($rows) => $rows->pluck('price_delta_try', 'option_value')->all())->all();
        $stockMapsByVariationId = $optionRowsByVariationId->map(fn ($rows) => $rows->pluck('stock_quantity', 'option_value')->all())->all();

        $newVariations = [];
        foreach ($current as $itemKey => $v) {
            $vid = $v['id'] ?? null;
            if ($vid === null || $vid === '') {
                $newVariations[$itemKey] = $v;
                continue;
            }

            $priceMap = $priceMapsByVariationId[(int) $vid] ?? $priceMapsByVariationId[(string) $vid] ?? [];
            $stockMap = $stockMapsByVariationId[(int) $vid] ?? $stockMapsByVariationId[(string) $vid] ?? [];
            if (! is_array($priceMap)) {
                $priceMap = [];
            }
            if (! is_array($stockMap)) {
                $stockMap = [];
            }

            // Mevcut satırın kopyası; sadece fiyat ve stok alanlarını güncelliyoruz
            $row = $v;

            // Bağımsız: options_with_prices
            $opts = $v['options_with_prices'] ?? [];
            if (is_array($opts)) {
                $row['options_with_prices'] = [];
                foreach ($opts as $rowItem) {
                    $val = (string) ($rowItem['option_value'] ?? '');
                    $delta = $val !== '' && array_key_exists($val, $priceMap)
                        ? (float) $priceMap[$val]
                        : (float) ($rowItem['price_delta_try'] ?? 0);
                    $stock = $val !== '' && array_key_exists($val, $stockMap)
                        ? $stockMap[$val]
                        : ($rowItem['stock_quantity'] ?? null);
                    $row['options_with_prices'][] = [
                        'option_value' => $rowItem['option_value'] ?? '',
                        'option_color' => $rowItem['option_color'] ?? null,
                        'option_image' => $rowItem['option_image'] ?? null,
                        'price_delta_try' => $delta,
                        'stock_quantity' => $stock,
                    ];
                }
            }

            // Bağlı: options_by_parent içindeki options[].price_delta_try
            $obp = $v['options_by_parent'] ?? [];
            if (is_array($obp)) {
                $row['options_by_parent'] = [];
                foreach ($obp as $parentRow) {
                    $newParentRow = $parentRow;
                    $inner = $parentRow['options'] ?? [];
                    if (is_array($inner)) {
                        $newParentRow['options'] = [];
                        foreach ($inner as $optRow) {
                            $val = (string) ($optRow['option_value'] ?? '');
                            $delta = $val !== '' && array_key_exists($val, $priceMap)
                                ? (float) $priceMap[$val]
                                : (float) ($optRow['price_delta_try'] ?? 0);
                            $stock = $val !== '' && array_key_exists($val, $stockMap)
                                ? $stockMap[$val]
                                : ($optRow['stock_quantity'] ?? null);
                            $newParentRow['options'][] = [
                                'option_value' => $optRow['option_value'] ?? '',
                                'option_color' => $optRow['option_color'] ?? null,
                                'option_image' => $optRow['option_image'] ?? null,
                                'price_delta_try' => $delta,
                                'stock_quantity' => $stock,
                            ];
                        }
                    }
                    $row['options_by_parent'][] = $newParentRow;
                }
            }

            $newVariations[$itemKey] = $row;
        }

        // Tam atama: Livewire'ın değişikliği kesin görmesi için
        $this->data['variations'] = $newVariations;
    }

    private function syncVariationOptionPrices(Product $product): void
    {
        $formMap = [];
        foreach ($this->formVariations as $v) {
            $name = $v['name'] ?? null;
            if (! $name) {
                continue;
            }
            $formMap[$name] = $v;
        }

        foreach ($product->variations as $variation) {
            $existing = ProductVariationOptionPrice::where('product_variation_id', $variation->id)->get()->keyBy('option_value');
            $vState = $formMap[$variation->name] ?? null;

            $optionData = [];
            foreach (($vState['options_with_prices'] ?? []) as $row) {
                $val = $row['option_value'] ?? null;
                if ($val === null || $val === '') {
                    continue;
                }
                $val = (string) $val;
                $d = $row['price_delta_try'] ?? null;
                $delta = $d !== null && $d !== '' && $d !== false ? (float) $d : (float) ($existing->get($val)?->price_delta_try ?? 0);
                $stock = isset($row['stock_quantity']) && $row['stock_quantity'] !== '' && $row['stock_quantity'] !== null ? (int) $row['stock_quantity'] : null;
                $optionData[$val] = ['price_delta_try' => $delta, 'stock_quantity' => $stock];
            }

            $obp = $variation->getOptionsByParentRaw();
            if (is_array($obp) && isset($obp[0]['options'])) {
                foreach ($obp as $parentRow) {
                    foreach ($parentRow['options'] ?? [] as $row) {
                        $val = $row['option_value'] ?? null;
                        if ($val === null || $val === '') {
                            continue;
                        }
                        $val = (string) $val;
                        $d = $row['price_delta_try'] ?? null;
                        $delta = $d !== null && $d !== '' && $d !== false ? (float) $d : (float) ($existing->get($val)?->price_delta_try ?? 0);
                        $stock = isset($row['stock_quantity']) && $row['stock_quantity'] !== '' && $row['stock_quantity'] !== null ? (int) $row['stock_quantity'] : null;
                        $optionData[$val] = ['price_delta_try' => $delta, 'stock_quantity' => $stock];
                    }
                }
            }

            foreach ($optionData as $val => $data) {
                ProductVariationOptionPrice::updateOrCreate(
                    ['product_variation_id' => $variation->id, 'option_value' => $val],
                    ['price_delta_try' => $data['price_delta_try'], 'stock_quantity' => $data['stock_quantity']]
                );
            }
            if ($optionData !== []) {
                foreach ($existing as $val => $row) {
                    if (! array_key_exists($val, $optionData)) {
                        $row->delete();
                    }
                }
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

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
