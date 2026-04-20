<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use App\Models\ProductImage;
use Filament\Resources\Pages\CreateRecord;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    public static function getNavigationLabel(): string
    {
        return 'Yeni Ürün Ekle';
    }

    protected ?string $maxContentWidth = 'full';

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
