<?php

namespace App\Filament\Resources\InterfaceColorVariationResource\Pages;

use App\Filament\Resources\InterfaceColorVariationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInterfaceColorVariation extends EditRecord
{
    protected static string $resource = InterfaceColorVariationResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        return InterfaceColorVariationResource::finalizeFormData($data);
    }

    protected function afterSave(): void
    {
        $fabricId = $this->record->interface_fabric_type_variation_id;
        if ($fabricId !== null) {
            $this->record->fabricTypeVariations()->syncWithoutDetaching([(int) $fabricId]);
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
