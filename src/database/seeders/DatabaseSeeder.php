<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $company = Company::firstOrCreate(
            ['code' => 'DEMO'],
            ['name' => 'Demo Şirket', 'is_active' => true]
        );

        // Şifreyi düz metin veriyoruz; User modelindeki 'hashed' cast tek sefer hashleyecek.
        // updateOrCreate: varsa şifreyi de günceller (yanlış hash ile oluşturulmuşsa düzeltir).
        User::updateOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'company_id' => $company->id,
                'name' => 'Admin',
                'password' => 'password',
                'is_admin' => true,
            ]
        );

        // Demo müşteri (panel girişi: customer@demo.com / password)
        User::updateOrCreate(
            ['email' => 'customer@demo.com'],
            [
                'company_id' => $company->id,
                'name' => 'Demo Müşteri',
                'password' => 'password',
                'is_admin' => false,
            ]
        );

        $this->call(CurrencySeeder::class);
        $this->call(TaxSeeder::class);
        $this->call(CategorySeeder::class);
        $this->call(ProductSeeder::class);
        $this->call(HomeContentSeeder::class);
        $this->call(FooterMenuSeeder::class);
    }
}
