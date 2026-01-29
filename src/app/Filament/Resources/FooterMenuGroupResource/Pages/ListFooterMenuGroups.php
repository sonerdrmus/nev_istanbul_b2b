<?php

namespace App\Filament\Resources\FooterMenuGroupResource\Pages;

use App\Filament\Resources\FooterMenuGroupResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFooterMenuGroups extends ListRecords
{
    protected static string $resource = FooterMenuGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
