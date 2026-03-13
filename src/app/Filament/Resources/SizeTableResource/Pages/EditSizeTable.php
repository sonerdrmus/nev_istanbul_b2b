<?php

namespace App\Filament\Resources\SizeTableResource\Pages;

use App\Filament\Resources\SizeTableResource;
use Filament\Resources\Pages\EditRecord;

class EditSizeTable extends EditRecord
{
    protected static string $resource = SizeTableResource::class;

    /** Form açılırken kayıttan trigger_combined alanını doldur (Select'te seçili görünsün). */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $variation = trim((string) ($data['trigger_variation_name'] ?? ''));
        $option = trim((string) ($data['trigger_option_value'] ?? ''));
        $data['trigger_combined'] = $variation !== ''
            ? ($option !== '' ? $variation . '|' . $option : $variation)
            : null;
        return $data;
    }

    /** Kaydetmeden önce trigger_combined değerini trigger_variation_name + trigger_option_value olarak böl. */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $fullState = $this->form->getState();
        $combined = $fullState['trigger_combined'] ?? $data['trigger_combined'] ?? '';
        if (is_string($combined) && $combined !== '') {
            if (str_contains($combined, '|')) {
                $parts = explode('|', $combined, 2);
                $data['trigger_variation_name'] = trim($parts[0]);
                $data['trigger_option_value'] = trim($parts[1]) ?: null;
            } else {
                $data['trigger_variation_name'] = trim($combined);
                $data['trigger_option_value'] = null;
            }
        }
        unset($data['trigger_combined']);
        return $data;
    }
}
