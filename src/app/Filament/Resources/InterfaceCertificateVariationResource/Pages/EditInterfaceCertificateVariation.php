<?php

namespace App\Filament\Resources\InterfaceCertificateVariationResource\Pages;

use App\Filament\Resources\InterfaceCertificateVariationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInterfaceCertificateVariation extends EditRecord
{
    protected static string $resource = InterfaceCertificateVariationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
