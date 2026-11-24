<?php

namespace App\Filament\Resources\InterfaceProductModelVariationResource\Pages;

use App\Filament\Resources\InterfaceProductModelVariationResource;
use App\Support\ProductVariationOptionInterfaceSync;
use Filament\Resources\Pages\CreateRecord;

class CreateInterfaceProductModelVariation extends CreateRecord
{
    protected static string $resource = InterfaceProductModelVariationResource::class;

    /**
     * Ürün atamaları (pivot) kayıttan sonra yazıldığı için mutabakat burada yapılır:
     * yeni model, ait olduğu ürünlerin Model varyasyonlarına otomatik eklenir.
     */
    protected function afterCreate(): void
    {
        ProductVariationOptionInterfaceSync::reconcileProductModelProductOptions(presetId: (int) $this->record->getKey());
    }
}
