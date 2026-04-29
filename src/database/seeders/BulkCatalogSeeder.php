<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Company;
use App\Models\Currency;
use App\Models\Product;
use App\Models\TaxClass;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class BulkCatalogSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::where('code', 'DEMO')->first();
        if (! $company) {
            return;
        }

        $currencyId = Currency::getDefault()?->id;
        $taxClassId = TaxClass::first()?->id;

        $categoryMap = [
            ['name' => 'Çantalar', 'slug' => 'cantalar', 'parent_slug' => 'aksesuar', 'sort_order' => 10],
            ['name' => 'Şapkalar', 'slug' => 'sapkalar', 'parent_slug' => 'aksesuar', 'sort_order' => 11],
            ['name' => 'İş Giyim', 'slug' => 'is-giyim', 'parent_slug' => 'ust-giyim', 'sort_order' => 12],
            ['name' => 'Ev Tekstili', 'slug' => 'ev-tekstili', 'parent_slug' => 'aksesuar', 'sort_order' => 13],
            ['name' => 'Promosyon', 'slug' => 'promosyon', 'parent_slug' => 'aksesuar', 'sort_order' => 14],
            ['name' => 'Tekstil Aksesuarları', 'slug' => 'tekstil-aksesuarlari', 'parent_slug' => 'aksesuar', 'sort_order' => 15],
        ];

        foreach ($categoryMap as $categoryData) {
            $parentId = Category::where('slug', $categoryData['parent_slug'])->value('id');
            Category::updateOrCreate(
                ['slug' => $categoryData['slug']],
                [
                    'name' => $categoryData['name'],
                    'parent_id' => $parentId,
                    'sort_order' => $categoryData['sort_order'],
                    'is_active' => true,
                ]
            );
        }

        $products = [
            ['name' => 'Bez Çantalar', 'category_slug' => 'cantalar'],
            ['name' => 'İmperteks Çantalar', 'category_slug' => 'cantalar'],
            ['name' => 'Beyzball Şapkalar', 'category_slug' => 'sapkalar'],
            ['name' => 'Bucket Hats', 'category_slug' => 'sapkalar'],
            ['name' => 'Hiphop Hats', 'category_slug' => 'sapkalar'],
            ['name' => 'Bere', 'category_slug' => 'sapkalar'],
            ['name' => 'Çorap', 'category_slug' => 'tekstil-aksesuarlari'],
            ['name' => 'İş Önlüğü', 'category_slug' => 'is-giyim'],
            ['name' => 'Gömleklek', 'category_slug' => 'is-giyim'],
            ['name' => 'Forma', 'category_slug' => 'is-giyim'],
            ['name' => 'Yelek', 'category_slug' => 'is-giyim'],
            ['name' => 'Bahçıvan', 'category_slug' => 'is-giyim'],
            ['name' => 'Triko Kazak', 'category_slug' => 'is-giyim'],
            ['name' => 'Havlu', 'category_slug' => 'tekstil-aksesuarlari'],
            ['name' => 'Mont', 'category_slug' => 'is-giyim'],
            ['name' => 'İş Pantolon', 'category_slug' => 'is-giyim'],
            ['name' => 'Masa Örtüsü', 'category_slug' => 'ev-tekstili'],
            ['name' => 'Kırlent', 'category_slug' => 'ev-tekstili'],
            ['name' => 'Panço', 'category_slug' => 'tekstil-aksesuarlari'],
            ['name' => 'Runner', 'category_slug' => 'ev-tekstili'],
            ['name' => 'Buff', 'category_slug' => 'sapkalar'],
            ['name' => 'Peçete', 'category_slug' => 'ev-tekstili'],
            ['name' => 'Bandana', 'category_slug' => 'sapkalar'],
            ['name' => 'Triko Atkı', 'category_slug' => 'tekstil-aksesuarlari'],
            ['name' => 'Önlük', 'category_slug' => 'is-giyim'],
            ['name' => 'Tulum', 'category_slug' => 'is-giyim'],
            ['name' => 'Kupa', 'category_slug' => 'promosyon'],
            ['name' => 'Amerikan Servis', 'category_slug' => 'ev-tekstili'],
            ['name' => 'Bornoz', 'category_slug' => 'tekstil-aksesuarlari'],
        ];

        $imageDirectory = storage_path('app/public/products/samples');
        File::ensureDirectoryExists($imageDirectory);

        foreach ($products as $index => $productData) {
            $slug = Str::slug($productData['name']);
            $categoryId = Category::where('slug', $productData['category_slug'])->value('id');

            if (! $categoryId) {
                continue;
            }

            $imageRelativePath = 'products/samples/' . $slug . '.svg';
            $imageAbsolutePath = storage_path('app/public/' . $imageRelativePath);
            File::put($imageAbsolutePath, $this->buildSampleSvg($productData['name']));

            Product::updateOrCreate(
                [
                    'company_id' => $company->id,
                    'slug' => $slug,
                ],
                [
                    'name' => $productData['name'],
                    'category_id' => $categoryId,
                    'currency_id' => $currencyId,
                    'tax_class_id' => $taxClassId,
                    'description' => $productData['name'] . ' için örnek ürün kaydı.',
                    'price' => 100 + ($index * 15),
                    'stock_quantity' => 100,
                    'minimum_order_quantity' => 1,
                    'image' => $imageRelativePath,
                    'status' => 'satista',
                    'is_active' => true,
                    'sort_order' => 300 + $index,
                ]
            );
        }
    }

    private function buildSampleSvg(string $title): string
    {
        $safeTitle = e($title);

        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="1200" height="1200" viewBox="0 0 1200 1200">
  <defs>
    <linearGradient id="bg" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0%" stop-color="#0f4c81"/>
      <stop offset="100%" stop-color="#2a7ab9"/>
    </linearGradient>
  </defs>
  <rect width="1200" height="1200" fill="url(#bg)"/>
  <rect x="110" y="110" width="980" height="980" rx="42" fill="rgba(255,255,255,0.12)"/>
  <text x="600" y="560" text-anchor="middle" font-size="76" font-family="Arial, sans-serif" fill="#ffffff" font-weight="700">{$safeTitle}</text>
  <text x="600" y="650" text-anchor="middle" font-size="34" font-family="Arial, sans-serif" fill="rgba(255,255,255,0.85)">Örnek Ürün Görseli</text>
</svg>
SVG;
    }
}
