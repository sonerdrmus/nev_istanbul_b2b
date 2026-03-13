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
}
