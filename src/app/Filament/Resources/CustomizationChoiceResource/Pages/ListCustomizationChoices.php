<?php

namespace App\Filament\Resources\CustomizationChoiceResource\Pages;

use App\Filament\Resources\CustomizationChoiceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCustomizationChoices extends ListRecords
{
    protected static string $resource = CustomizationChoiceResource::class;

    protected static ?string $title = 'Özelleştirme Seçeneği';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Yeni özelleştirme seçeneği'),
        ];
    }
}
