<?php

namespace App\Filament\Resources\InterfaceDeliveryMethodVariationResource\Pages;

use App\Filament\Resources\InterfaceDeliveryMethodVariationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInterfaceDeliveryMethodVariation extends EditRecord
{
    protected static string $resource = InterfaceDeliveryMethodVariationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
