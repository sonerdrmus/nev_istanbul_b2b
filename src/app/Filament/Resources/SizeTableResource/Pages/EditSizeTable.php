<?php

namespace App\Filament\Resources\SizeTableResource\Pages;

use App\Filament\Resources\SizeTableResource;
use App\Support\ProductVariationOptionInterfaceSync;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSizeTable extends EditRecord
{
    protected static string $resource = SizeTableResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    /** Form açılırken kayıttan trigger_combined alanını doldur (Select'te seçili görünsün). */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $variation = trim((string) ($data['trigger_variation_name'] ?? ''));
        $option = trim((string) ($data['trigger_option_value'] ?? ''));
        $data['trigger_combined'] = $variation !== ''
            ? ($option !== '' ? $variation.'|'.$option : $variation)
            : null;

        return $data;
    }

    /** Kaydetmeden önce trigger_combined değerini trigger_variation_name + trigger_option_value olarak böl. */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $fullState = $this->form->getRawState();
        $combined = $fullState['trigger_combined'] ?? $data['trigger_combined'] ?? '';

        return SizeTableResource::applyTriggerCombinedToFormData($data, $combined);
    }

    protected function afterSave(): void
    {
        ProductVariationOptionInterfaceSync::reconcileSizeTableProductOptions(presetId: (int) $this->record->getKey());
    }
}
