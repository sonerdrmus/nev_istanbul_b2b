<?php

namespace App\Filament\Forms;

use Filament\Forms;

/**
 * EN/IT display-name inputs. The TR `name` / `option_value` field stays the matching key.
 */
final class LocaleNameInputs
{
    /**
     * @return array<int, Forms\Components\TextInput>
     */
    public static function make(
        string $base = 'name',
        string $labelEn = 'Ad (EN)',
        string $labelIt = 'Ad (IT)',
    ): array {
        $helper = 'Yalnızca mağaza gösterimi. TR ad / eşleştirme alanını değiştirmez.';

        return [
            Forms\Components\TextInput::make($base.'_en')
                ->label($labelEn)
                ->maxLength(255)
                ->helperText($helper),
            Forms\Components\TextInput::make($base.'_it')
                ->label($labelIt)
                ->maxLength(255)
                ->helperText($helper),
        ];
    }
}
