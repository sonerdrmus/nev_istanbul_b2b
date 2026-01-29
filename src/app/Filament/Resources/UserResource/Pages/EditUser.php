<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\Company;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Hash;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['company_profit_margin_percentage'] = $this->record->company?->profit_margin_percentage ?? 0;

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        if (! empty($data['company_id']) && array_key_exists('company_profit_margin_percentage', $data)) {
            Company::where('id', $data['company_id'])->update([
                'profit_margin_percentage' => (float) $data['company_profit_margin_percentage'],
            ]);
        }
        unset($data['company_profit_margin_percentage']);

        return $data;
    }
}
