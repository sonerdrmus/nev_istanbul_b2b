<?php

namespace App\Filament\Resources\CustomizationChoiceResource\Pages;

use App\Filament\Resources\CustomizationChoiceResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCustomizationChoice extends CreateRecord
{
    protected static string $resource = CustomizationChoiceResource::class;

    protected static ?string $title = 'Yeni özelleştirme seçeneği';
}
