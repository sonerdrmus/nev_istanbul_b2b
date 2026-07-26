<?php

namespace App\Filament\Resources\InterfaceFabricTypeVariationResource\Pages;

use App\Filament\Resources\InterfaceFabricTypeVariationResource;
use App\Support\ProductVariationOptionInterfaceSync;
use Filament\Resources\Pages\CreateRecord;

class CreateInterfaceFabricTypeVariation extends CreateRecord
{
    protected static string $resource = InterfaceFabricTypeVariationResource::class;

    /**
     * Ürün atamaları (pivot) kayıttan sonra yazıldığı için mutabakat burada yapılır:
     * yeni kumaş, ait olduğu ürünlerin Kumaş türü varyasyonlarına otomatik eklenir.
     */
    protected function afterCreate(): void
    {
        ProductVariationOptionInterfaceSync::reconcileFabricProductOptions(presetId: (int) $this->record->getKey());
    }
}
