<?php

namespace App\Filament\Resources\InterfaceColorVariationResource\Pages;

use App\Filament\Resources\InterfaceColorVariationResource;
use App\Filament\Resources\ProductResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateInterfaceColorVariation extends CreateRecord
{
    protected static string $resource = InterfaceColorVariationResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return InterfaceColorVariationResource::finalizeFormData($data);
    }

    protected function afterCreate(): void
    {
        $fabricId = $this->record->interface_fabric_type_variation_id;
        if ($fabricId !== null) {
            $this->record->fabricTypeVariations()->syncWithoutDetaching([(int) $fabricId]);
        }

        $variationCount = ProductResource::appendColorVariationOptionFromInterfacePreset($this->record);

        if ($variationCount === 0) {
            return;
        }

        Notification::make()
            ->success()
            ->title('Ürün renk seçenekleri güncellendi')
            ->body("Yeni renk, {$variationCount} Renk varyasyonuna seçenek olarak eklendi.")
            ->send();
    }
}
