<?php

namespace App\Filament\Resources\InterfaceProductModelVariationResource\Pages;

use App\Filament\Resources\InterfaceProductModelVariationResource;
use App\Support\ProductVariationOptionInterfaceSync;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInterfaceProductModelVariation extends EditRecord
{
    protected static string $resource = InterfaceProductModelVariationResource::class;

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
        ProductVariationOptionInterfaceSync::reconcileProductModelProductOptions(presetId: (int) $this->record->getKey());
    }
}
