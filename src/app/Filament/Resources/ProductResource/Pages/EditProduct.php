<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
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
        $data['_product_id'] = $this->record->getKey();

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

        $data = ProductResource::finalizeVariationOptionsInProductFormData($data);

        return $data;
    }

    protected function afterSave(): void
    {
        ProductResource::syncVariationOptionImagesAfterFilamentSave($this->record, $this->form->getState());
    }
}
