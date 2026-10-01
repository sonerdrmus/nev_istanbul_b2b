<?php

namespace App\Filament\Resources\ExtraVariationResource\Pages;

use App\Filament\Resources\ExtraVariationResource;
use App\Models\ExtraVariation;
use Filament\Resources\Pages\CreateRecord;

class CreateExtraVariation extends CreateRecord
{
    protected static string $resource = ExtraVariationResource::class;

    protected static ?string $title = 'Yeni varyasyon ekle';

    protected function afterCreate(): void
    {
        $this->clearUnusedAnswerData();
    }

    private function clearUnusedAnswerData(): void
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
