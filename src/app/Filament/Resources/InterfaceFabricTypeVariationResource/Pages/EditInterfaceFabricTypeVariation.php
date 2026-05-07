<?php

namespace App\Filament\Resources\InterfaceFabricTypeVariationResource\Pages;

use App\Filament\Resources\InterfaceFabricTypeVariationResource;
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
}
