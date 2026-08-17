<?php

namespace App\Services;

use App\Models\Company;
use App\Models\DealerRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class DealerRequestApprover
{
    /**
     * @return array{user: User, generated_password: ?string}
     */
    public function approve(DealerRequest $record, ?int $approvedBy = null): array
    {
        if ($record->status !== 'pending') {
            throw new RuntimeException('Bu talep zaten işlenmiş.');
        }

        if (User::where('email', $record->email)->exists()) {
            throw new RuntimeException('Bu e-posta ile kullanıcı zaten var.');
        }

        return DB::transaction(function () use ($record, $approvedBy) {
            do {
                $code = 'BAYI-'.strtoupper(Str::random(6));
            } while (Company::where('code', $code)->exists());

            $company = Company::create([
                'name' => (string) ($record->business_name ?: $record->full_name),
                'code' => $code,
                'is_active' => true,
            ]);

            $generatedPassword = null;
            $userAttributes = [
                'company_id' => $company->id,
                'name' => $record->applicantDisplayName(),
                'email' => $record->email,
                'is_admin' => false,
                'is_approved' => true,
            ];

            if (filled($record->password)) {
                $user = User::create([...$userAttributes, 'password' => Str::random(32)]);
                DB::table('users')->where('id', $user->id)->update(['password' => $record->password]);
                $user->refresh();
            } else {
                $generatedPassword = Str::random(10);
                $user = User::create([...$userAttributes, 'password' => $generatedPassword]);
            }

            $record->update([
                'status' => 'approved',
                'approved_at' => now(),
                'approved_by' => $approvedBy,
                'created_company_id' => $company->id,
                'created_user_id' => $user->id,
            ]);

            return [
                'user' => $user,
                'generated_password' => $generatedPassword,
            ];
        });
    }
}
