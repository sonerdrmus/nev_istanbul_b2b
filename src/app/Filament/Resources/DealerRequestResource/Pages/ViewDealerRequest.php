<?php

namespace App\Filament\Resources\DealerRequestResource\Pages;

use App\Filament\Resources\DealerRequestResource;
use App\Models\Company;
use App\Models\DealerRequest;
use App\Models\User;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Str;

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
                ->action(function (): void {
                    /** @var DealerRequest $record */
                    $record = $this->record;

                    if ($record->status !== 'pending') {
                        return;
                    }
                    if (User::where('email', $record->email)->exists()) {
                        Notification::make()->title('Bu e-posta ile kullanıcı zaten var')->danger()->send();
                        return;
                    }
                    $passwordPlain = Str::random(10);
                    do {
                        $code = 'BAYI-' . strtoupper(Str::random(6));
                    } while (Company::where('code', $code)->exists());

                    $companyName = (string) ($record->business_name ?: $record->full_name);

                    $company = Company::create([
                        'name' => $companyName,
                        'code' => $code,
                        'is_active' => true,
                    ]);

                    $user = User::create([
                        'company_id' => $company->id,
                        'name' => $record->applicantDisplayName(),
                        'email' => $record->email,
                        'password' => $passwordPlain,
                        'is_admin' => false,
                    ]);

                    $record->update([
                        'status' => 'approved',
                        'approved_at' => now(),
                        'approved_by' => auth()->id(),
                        'created_company_id' => $company->id,
                        'created_user_id' => $user->id,
                    ]);

                    Notification::make()
                        ->title('Bayilik talebi onaylandı')
                        ->body("Kullanıcı oluşturuldu. Şifre: {$passwordPlain}")
                        ->success()
                        ->send();
                }),
        ];
    }
}

