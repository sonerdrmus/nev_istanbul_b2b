<?php

namespace App\Filament\Resources\InterfaceColorVariationResource\Pages;

use App\Filament\Resources\InterfaceColorVariationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInterfaceColorVariation extends EditRecord
{
    protected static string $resource = InterfaceColorVariationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
