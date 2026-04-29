<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * Sıra: Şirket/Kullanıcı → Para birimi → Vergi → Kategori → Ürün (kategoriye bağlı) → Anasayfa → Footer.
     */
    public function run(): void
    {
        $company = Company::firstOrCreate(
            ['code' => 'DEMO'],
            ['name' => 'Demo Şirket', 'is_active' => true]
        );

        User::updateOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'company_id' => $company->id,
                'name' => 'Admin',
                'password' => 'password',
                'is_admin' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'customer@demo.com'],
            [
                'company_id' => $company->id,
                'name' => 'Demo Müşteri',
                'password' => 'password',
                'is_admin' => false,
            ]
        );

        $this->call([
            CurrencySeeder::class,
            TaxSeeder::class,
            CategorySeeder::class,
            ProductSeeder::class,
            BulkCatalogSeeder::class,
            SizeTableSeeder::class,
            HomeContentSeeder::class,
            FooterMenuSeeder::class,
        ]);
    }
}
