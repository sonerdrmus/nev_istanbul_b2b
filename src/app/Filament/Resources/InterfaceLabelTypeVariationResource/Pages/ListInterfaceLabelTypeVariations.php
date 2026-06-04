<?php

namespace App\Filament\Resources\InterfaceLabelTypeVariationResource\Pages;

use App\Filament\Resources\InterfaceLabelTypeVariationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListInterfaceLabelTypeVariations extends ListRecords
{
    protected static string $resource = InterfaceLabelTypeVariationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
