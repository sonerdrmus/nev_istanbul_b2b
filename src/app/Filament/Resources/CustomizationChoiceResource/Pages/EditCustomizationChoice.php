<?php

namespace App\Filament\Resources\CustomizationChoiceResource\Pages;

use App\Filament\Resources\CustomizationChoiceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCustomizationChoice extends EditRecord
{
    protected static string $resource = CustomizationChoiceResource::class;

    protected static ?string $title = 'Özelleştirme seçeneğini düzenle';

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
