<?php

namespace App\Filament\Resources\SizeTableResource\Pages;

use App\Filament\Resources\SizeTableResource;
use App\Models\SizeTable;
use App\Support\ProductVariationOptionInterfaceSync;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateSizeTable extends CreateRecord
{
    protected static string $resource = SizeTableResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $fullState = $this->form->getRawState();
        $combined = $fullState['trigger_combined'] ?? $data['trigger_combined'] ?? '';
        $data = SizeTableResource::applyTriggerCombinedToFormData($data, $combined);

        $base = Str::slug((string) ($data['name'] ?? 'beden-tablosu'));
        if ($base === '') {
            $base = 'beden-tablosu';
        }
        $slug = $base;
        $i = 1;
        while (SizeTable::query()->where('slug', $slug)->exists()) {
            $slug = $base.'-'.$i;
            $i++;
        }
        $data['slug'] = $slug;
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);

        return $data;
    }

    protected function afterCreate(): void
    {
        ProductVariationOptionInterfaceSync::reconcileSizeTableProductOptions(presetId: (int) $this->record->getKey());
    }
}
