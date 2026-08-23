<?php

namespace App\Filament\Resources\InterfaceMoldModelVariationResource\Pages;

use App\Filament\Resources\InterfaceMoldModelVariationResource;
use App\Support\ProductVariationOptionInterfaceSync;
use Filament\Resources\Pages\CreateRecord;

class CreateInterfaceMoldModelVariation extends CreateRecord
{
    protected static string $resource = InterfaceMoldModelVariationResource::class;

    /**
     * Ürün atamaları (pivot) kayıttan sonra yazıldığı için mutabakat burada yapılır:
     * yeni kalıp, ait olduğu ürünlerin Kalıp Modeli varyasyonlarına otomatik eklenir.
     */
    protected function afterCreate(): void
    {
        ProductVariationOptionInterfaceSync::reconcileMoldModelProductOptions(presetId: (int) $this->record->getKey());
    }
}
