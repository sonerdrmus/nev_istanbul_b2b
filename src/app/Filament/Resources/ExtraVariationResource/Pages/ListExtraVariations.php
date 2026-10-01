<?php

namespace App\Filament\Resources\ExtraVariationResource\Pages;

use App\Filament\Resources\ExtraVariationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListExtraVariations extends ListRecords
{
    protected static string $resource = ExtraVariationResource::class;

    protected static ?string $title = 'Ekstra Varyasyon Oluştur';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Yeni varyasyon ekle'),
        ];
    }
}
