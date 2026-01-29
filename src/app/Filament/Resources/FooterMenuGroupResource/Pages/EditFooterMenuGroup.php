<?php

namespace App\Filament\Resources\FooterMenuGroupResource\Pages;

use App\Filament\Resources\FooterMenuGroupResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFooterMenuGroup extends EditRecord
{
    protected static string $resource = FooterMenuGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
