<?php

namespace App\Filament\Resources\InterfaceFabricTypeVariationResource\Pages;

use App\Filament\Resources\InterfaceFabricTypeVariationResource;
use App\Support\ProductVariationOptionInterfaceSync;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInterfaceFabricTypeVariation extends EditRecord
{
    protected static string $resource = InterfaceFabricTypeVariationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    /**
     * Ürün atamaları (pivot) kayıttan sonra yazıldığı için mutabakat burada yapılır:
     * atanan ürünlere seçenek eklenir, atamadan çıkarılan ürünlerden kaldırılır.
     */
    protected function afterSave(): void
    {
        ProductVariationOptionInterfaceSync::reconcileFabricProductOptions(presetId: (int) $this->record->getKey());
    }
}
