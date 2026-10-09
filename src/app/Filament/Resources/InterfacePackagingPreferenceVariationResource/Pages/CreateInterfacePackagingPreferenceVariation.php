<?php

namespace App\Filament\Resources\InterfacePackagingPreferenceVariationResource\Pages;

use App\Filament\Resources\InterfacePackagingPreferenceVariationResource;
use App\Support\ProductVariationOptionInterfaceSync;
use Filament\Resources\Pages\CreateRecord;

class CreateInterfacePackagingPreferenceVariation extends CreateRecord
{
    protected static string $resource = InterfacePackagingPreferenceVariationResource::class;

    protected function afterCreate(): void
    {
        ProductVariationOptionInterfaceSync::reconcilePackagingProductOptions(presetId: (int) $this->record->getKey());
    }
}
