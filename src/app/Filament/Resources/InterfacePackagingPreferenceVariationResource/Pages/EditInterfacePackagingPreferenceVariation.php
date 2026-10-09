<?php

namespace App\Filament\Resources\InterfacePackagingPreferenceVariationResource\Pages;

use App\Filament\Resources\InterfacePackagingPreferenceVariationResource;
use App\Support\ProductVariationOptionInterfaceSync;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInterfacePackagingPreferenceVariation extends EditRecord
{
    protected static string $resource = InterfacePackagingPreferenceVariationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        ProductVariationOptionInterfaceSync::reconcilePackagingProductOptions(presetId: (int) $this->record->getKey());
    }
}
