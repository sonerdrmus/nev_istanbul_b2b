<?php

namespace App\Filament\Resources\InterfaceFabricTypeVariationResource\Pages;

use App\Filament\Resources\InterfaceFabricTypeVariationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListInterfaceFabricTypeVariations extends ListRecords
{
    protected static string $resource = InterfaceFabricTypeVariationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
