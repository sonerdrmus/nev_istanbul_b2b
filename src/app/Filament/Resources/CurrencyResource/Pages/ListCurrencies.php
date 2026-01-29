<?php

namespace App\Filament\Resources\CurrencyResource\Pages;

use App\Filament\Resources\CurrencyResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Artisan;

class ListCurrencies extends ListRecords
{
    protected static string $resource = CurrencyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('updateRates')
                ->label('Şimdi TCMB\'den Güncelle')
                ->icon('heroicon-o-arrow-path')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Döviz Kurlarını Güncelle')
                ->modalDescription('Kurlar zaten her saat otomatik güncellenir. Hemen şimdi TCMB\'den çekmek istiyor musunuz?')
                ->action(function () {
                    Artisan::call('currency:update-rates');
                    $output = Artisan::output();
                    Notification::make()
                        ->title('Kurlar güncellendi')
                        ->body($output ?: 'TCMB\'den kurlar başarıyla güncellendi.')
                        ->success()
                        ->send();
                }),
            Actions\CreateAction::make(),
        ];
    }
}
