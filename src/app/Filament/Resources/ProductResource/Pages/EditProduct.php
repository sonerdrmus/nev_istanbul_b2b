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

        // Varyasyon seçeneklerinde: option_image_upload varsa option_image'ı onunla güncelle.
        if (! empty($data['variations']) && is_array($data['variations'])) {
            foreach ($data['variations'] as &$variation) {
                if (empty($variation['options']) || ! is_array($variation['options'])) {
                    continue;
                }

                foreach ($variation['options'] as &$opt) {
                    if (! empty($opt['option_image_upload'])) {
                        $upload = $opt['option_image_upload'];
                        $path = is_array($upload) ? ($upload[0] ?? null) : $upload;
                        if (is_string($path) && trim($path) !== '') {
                            $opt['option_image'] = $path;
                        }
                    }
                    unset($opt['option_image_upload']);
                }
            }
            unset($variation, $opt);
        }

        return $data;
    }
}
