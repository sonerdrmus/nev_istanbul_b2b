<?php

namespace App\Filament\Resources\InterfaceMoldModelVariationResource\Pages;

use App\Filament\Resources\InterfaceMoldModelVariationResource;
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
}
