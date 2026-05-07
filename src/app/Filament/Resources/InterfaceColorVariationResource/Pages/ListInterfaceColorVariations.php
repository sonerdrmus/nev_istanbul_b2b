<?php

namespace App\Filament\Resources\InterfaceColorVariationResource\Pages;

use App\Filament\Resources\InterfaceColorVariationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListInterfaceColorVariations extends ListRecords
{
    protected static string $resource = InterfaceColorVariationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
