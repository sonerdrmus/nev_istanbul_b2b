<?php

namespace App\Filament\Resources\InterfacePackagingPreferenceVariationResource\Pages;

use App\Filament\Resources\InterfacePackagingPreferenceVariationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListInterfacePackagingPreferenceVariations extends ListRecords
{
    protected static string $resource = InterfacePackagingPreferenceVariationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
