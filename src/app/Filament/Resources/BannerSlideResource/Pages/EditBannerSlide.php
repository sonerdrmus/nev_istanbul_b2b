<?php

namespace App\Filament\Resources\BannerSlideResource\Pages;

use App\Filament\Resources\BannerSlideResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBannerSlide extends EditRecord
{
    protected static string $resource = BannerSlideResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
