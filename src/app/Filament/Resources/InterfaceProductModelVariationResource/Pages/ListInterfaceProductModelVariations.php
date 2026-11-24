<?php

namespace App\Filament\Resources\InterfaceProductModelVariationResource\Pages;

use App\Filament\Resources\InterfaceProductModelVariationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListInterfaceProductModelVariations extends ListRecords
{
    protected static string $resource = InterfaceProductModelVariationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
