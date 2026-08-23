<?php

namespace App\Filament\Resources\InterfaceMoldModelVariationResource\Pages;

use App\Filament\Resources\InterfaceMoldModelVariationResource;
use App\Support\ProductVariationOptionInterfaceSync;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInterfaceMoldModelVariation extends EditRecord
{
    protected static string $resource = InterfaceMoldModelVariationResource::class;

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
        ProductVariationOptionInterfaceSync::reconcileMoldModelProductOptions(presetId: (int) $this->record->getKey());
    }
}
