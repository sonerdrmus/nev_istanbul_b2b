<?php

namespace App\Filament\Resources\SizeTableResource\Pages;

use App\Filament\Resources\SizeTableResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSizeTables extends ListRecords
{
    protected static string $resource = SizeTableResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
