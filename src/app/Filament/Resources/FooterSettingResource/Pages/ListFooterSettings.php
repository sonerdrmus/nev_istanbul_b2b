<?php

namespace App\Filament\Resources\FooterSettingResource\Pages;

use App\Filament\Resources\FooterSettingResource;
use App\Models\FooterSetting;
use Filament\Resources\Pages\ListRecords;

class ListFooterSettings extends ListRecords
{
    protected static string $resource = FooterSettingResource::class;

    public function mount(): void
    {
        FooterSetting::get();
        parent::mount();
    }
}
