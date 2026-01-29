<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\Company;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Hash;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
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
