<?php

namespace App\Filament\Resources\DealerRequestResource\Pages;

use App\Filament\Resources\DealerRequestResource;
use App\Models\DealerRequest;
use App\Services\DealerRequestApprover;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewDealerRequest extends ViewRecord
{
    protected static string $resource = DealerRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('approve')
                ->label('Onayla')
                ->color('success')
                ->visible(fn (): bool => $this->record->status === 'pending')
                ->requiresConfirmation()
                ->action(function (DealerRequestApprover $approver): void {
                    /** @var DealerRequest $record */
                    $record = $this->record;

                    try {
                        $result = $approver->approve($record, auth()->id());
                    } catch (\RuntimeException $e) {
                        Notification::make()->title($e->getMessage())->danger()->send();

                        return;
                    }

                    $body = $result['generated_password']
                        ? 'Kullanıcı oluşturuldu. Şifre: '.$result['generated_password']
                        : 'Kullanıcı oluşturuldu. Başvuruda belirlediği şifre ile giriş yapabilir.';

                    Notification::make()
                        ->title('Bayilik talebi onaylandı')
                        ->body($body)
                        ->success()
                        ->send();
                }),
        ];
    }
}
