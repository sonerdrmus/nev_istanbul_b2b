<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use App\Support\ProductDimensionMultiplierSync;
use App\Support\ProductVariationOptionInterfaceSync;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    protected ?string $maxContentWidth = 'full';

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Kumaş / kalıp seçenekleri her açılışta güncel: yeni kayıtlar eklenir, başka ürüne özel olanlar düşer.
        ProductVariationOptionInterfaceSync::reconcileFabricProductOptions((int) $this->record->getKey());
        ProductVariationOptionInterfaceSync::reconcileMoldModelProductOptions((int) $this->record->getKey());
        ProductVariationOptionInterfaceSync::reconcileSizeTableProductOptions((int) $this->record->getKey());

        // Mutabakat sonrası taze veri gerektiği için loadMissing değil load kullanılır.
        $this->record->load([
            'variations' => fn ($query) => $query->orderBy('sort_order'),
            'variations.options' => fn ($query) => $query->orderBy('sort_order'),
            'customizationRows',
        ]);

        $data['_product_id'] = $this->record->getKey();
        $data['dimension_multipliers'] = ProductDimensionMultiplierSync::loadGroupedForForm(
            (int) $this->record->getKey(),
            fallbackToTemplate: true,
        );

        $data = ProductResource::ensureInterfacePresetOptionsInProductFormData($data, (int) $this->record->getKey());

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        unset($data['_product_id']);

        // Seçili mevcut görsel yerine yeni yüklenen varsa DB alanına onu yaz.
        if (! empty($data['image_upload'])) {
            $upload = $data['image_upload'];
            $path = is_array($upload) ? ($upload[0] ?? null) : $upload;
            if (is_string($path) && trim($path) !== '') {
                $data['image'] = $path;
            }
        }
        unset($data['image_upload']);

        if (! empty($data['home_showcase_image_upload'])) {
            $upload = $data['home_showcase_image_upload'];
            $path = is_array($upload) ? ($upload[0] ?? null) : $upload;
            if (is_string($path) && trim($path) !== '') {
                $data['home_showcase_image'] = $path;
            }
        }
        unset($data['home_showcase_image_upload']);

        // Ürün tablosunda kolon değil; afterSave'de persist edilir.
        unset($data['dimension_multipliers']);

        $data = ProductResource::finalizeVariationOptionsInProductFormData($data, (int) $this->record->getKey());

        return $data;
    }

    protected function afterSave(): void
    {
        $state = $this->form->getState();
        ProductResource::syncVariationOptionImagesAfterFilamentSave($this->record, $state);

        if (isset($state['dimension_multipliers']) && is_array($state['dimension_multipliers'])) {
            ProductDimensionMultiplierSync::persistGrouped(
                (int) $this->record->getKey(),
                $state['dimension_multipliers'],
            );
        }
    }
}
