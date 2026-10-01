<?php

namespace App\Filament\Resources\ExtraVariationResource\Pages;

use App\Filament\Resources\ExtraVariationResource;
use App\Models\ExtraVariation;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditExtraVariation extends EditRecord
{
    protected static string $resource = ExtraVariationResource::class;

    protected static ?string $title = 'Ekstra varyasyonu düzenle';

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $record = $this->getRecord();
        if (! $record instanceof ExtraVariation) {
            return;
        }

        if (! $record->isRadio()) {
            $record->options()->delete();
        }

        if ($record->answer_type !== ExtraVariation::TYPE_INFO && filled($record->info_text)) {
            $record->info_text = null;
            $record->save();
        }
    }
}
