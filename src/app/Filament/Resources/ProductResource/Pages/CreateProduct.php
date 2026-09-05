<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use App\Models\ProductImage;
use App\Support\ProductDimensionMultiplierSync;
use Filament\Resources\Pages\CreateRecord;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    public static function getNavigationLabel(): string
    {
        return 'Yeni Ürün Ekle';
    }

    protected ?string $maxContentWidth = 'full';

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['dimension_multipliers'] = ProductDimensionMultiplierSync::loadTemplateForNewProduct();

        return $data;
    }

    protected function afterCreate(): void
    {
        $state = $this->form->getState();
        $items = $state['productImages'] ?? [];
        foreach ($items as $i => $item) {
            if (! empty($item['path'] ?? null)) {
                ProductImage::create([
                    'product_id' => $this->record->getKey(),
                    'path' => $item['path'],
                    'sort_order' => (int) ($item['sort_order'] ?? $i),
                ]);
            }
        }

        ProductResource::syncVariationOptionImagesAfterFilamentSave($this->record, $this->form->getState());

    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        unset($data['dimension_multipliers']);

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
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
        unset($data['dimension_multipliers']);

        $data = ProductResource::finalizeVariationOptionsInProductFormData($data);

        return $data;
    }
}
