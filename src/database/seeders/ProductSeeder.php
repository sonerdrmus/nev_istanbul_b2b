<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Company;
use App\Models\Currency;
use App\Models\Product;
use App\Models\TaxClass;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::where('code', 'DEMO')->first();
        if (! $company) {
            return;
        }

        $products = [
            // Üst kategoriler için örnek ürünler
            [
                'name' => 'Üst Giyim - Örnek Paket',
                'slug' => 'ust-giyim-ornek-paket',
                'category_slug' => 'ust-giyim',
                'description' => 'Üst giyim kategorisi için örnek ürün.',
                'price' => 499.99,
                'sort_order' => 0,
                'stock_quantity' => 50,
                            ],
            [
                'name' => 'Alt Giyim - Örnek Paket',
                'slug' => 'alt-giyim-ornek-paket',
                'category_slug' => 'alt-giyim',
                'description' => 'Alt giyim kategorisi için örnek ürün.',
                'price' => 459.99,
                'sort_order' => 0,
                'stock_quantity' => 30,
                            ],
            [
                'name' => 'Dış Giyim - Örnek Paket',
                'slug' => 'dis-giyim-ornek-paket',
                'category_slug' => 'dis-giyim',
                'description' => 'Dış giyim kategorisi için örnek ürün.',
                'price' => 699.99,
                'sort_order' => 0,
                'stock_quantity' => 0,
                            ],
            [
                'name' => 'Kadın Giyim - Örnek Paket',
                'slug' => 'kadin-giyim-ornek-paket',
                'category_slug' => 'kadin-giyim',
                'description' => 'Kadın giyim kategorisi için örnek ürün.',
                'price' => 549.99,
                'sort_order' => 0,
                'stock_quantity' => 25,
                            ],
            [
                'name' => 'Aksesuar - Örnek Paket',
                'slug' => 'aksesuar-ornek-paket',
                'category_slug' => 'aksesuar',
                'description' => 'Aksesuar kategorisi için örnek ürün.',
                'price' => 199.99,
                'sort_order' => 0,
                'stock_quantity' => null,
                            ],
            [
                'name' => 'Klasik Tişört',
                'slug' => 'klasik-tisort',
                'category_slug' => 'tisort',
                'description' => 'Günlük kullanım için pamuklu tişört. Yaka ve cinsiyete göre renk seçenekleri.',
                'price' => 149.99,
                'sort_order' => 1,
                'stock_quantity' => 100,
                'variations' => [
                    ['name' => 'Yaka Tipi', 'type' => 'image', 'depends_on' => null, 'options' => ['O yaka', 'V yaka'], 'options_by_parent' => null, 'sort_order' => 1, 'option_meta' => ['O yaka' => ['image' => 'variation_options/o-yaka.png'], 'V yaka' => ['image' => 'variation_options/v-yaka.png']]],
                    ['name' => 'Erkek/Bayan', 'type' => 'select', 'depends_on' => null, 'options' => ['Erkek', 'Bayan'], 'options_by_parent' => null, 'sort_order' => 2],
                    ['name' => 'Renk', 'type' => 'color', 'depends_on' => 'Erkek/Bayan', 'options' => [], 'options_by_parent' => ['Erkek' => ['Sarı', 'Mavi', 'Lacivert'], 'Bayan' => ['Pembe', 'Mor', 'Eflatun']], 'sort_order' => 3],
                ],
            ],
            [
                'name' => 'Polo Yaka Tişört',
                'slug' => 'polo-yaka-tisort',
                'category_slug' => 'tisort',
                'description' => 'Polo yaka veya düz yaka seçeneği. Cinsiyete göre beden.',
                'price' => 189.99,
                'sort_order' => 2,
                'stock_quantity' => 80,
                'variations' => [
                    ['name' => 'Yaka', 'type' => 'select', 'depends_on' => null, 'options' => ['Polo', 'Düz'], 'options_by_parent' => null, 'sort_order' => 1],
                    ['name' => 'Cinsiyet', 'type' => 'select', 'depends_on' => null, 'options' => ['Erkek', 'Kadın'], 'options_by_parent' => null, 'sort_order' => 2],
                    ['name' => 'Beden', 'type' => 'select', 'depends_on' => 'Cinsiyet', 'options' => [], 'options_by_parent' => ['Erkek' => ['S', 'M', 'L', 'XL'], 'Kadın' => ['36', '38', '40', '42']], 'sort_order' => 3],
                ],
            ],
            [
                'name' => 'Oxford Gömlek',
                'slug' => 'oxford-gomlek',
                'category_slug' => 'gomlek',
                'description' => 'Resmi ve yarı resmi kullanım için gömlek. Yaka ve cinsiyete göre renk.',
                'price' => 299.99,
                'sort_order' => 3,
                'stock_quantity' => 45,
                'variations' => [
                    ['name' => 'Yaka', 'type' => 'select', 'depends_on' => null, 'options' => ['Düz', 'İtalyan'], 'options_by_parent' => null, 'sort_order' => 1],
                    ['name' => 'Erkek/Bayan', 'type' => 'select', 'depends_on' => null, 'options' => ['Erkek', 'Bayan'], 'options_by_parent' => null, 'sort_order' => 2],
                    ['name' => 'Renk', 'type' => 'color', 'depends_on' => 'Erkek/Bayan', 'options' => [], 'options_by_parent' => ['Erkek' => ['Lacivert', 'Beyaz', 'Gri', 'Pembe'], 'Bayan' => ['Eflatun', 'Beyaz', 'Pembe', 'Lacivert']], 'sort_order' => 3],
                ],
            ],
            [
                'name' => 'Sweatshirt',
                'slug' => 'sweatshirt',
                'category_slug' => 'sweatshirt',
                'description' => 'Kapüşonlu veya kapüşonsuz sweatshirt. Cinsiyete göre renk paleti.',
                'price' => 249.99,
                'sort_order' => 4,
                'stock_quantity' => 60,
                'variations' => [
                    ['name' => 'Kapüşon', 'type' => 'select', 'depends_on' => null, 'options' => ['Var', 'Yok'], 'options_by_parent' => null, 'sort_order' => 1],
                    ['name' => 'Cinsiyet', 'type' => 'select', 'depends_on' => null, 'options' => ['Erkek', 'Kadın'], 'options_by_parent' => null, 'sort_order' => 2],
                    ['name' => 'Renk', 'type' => 'color', 'depends_on' => 'Cinsiyet', 'options' => [], 'options_by_parent' => ['Erkek' => ['Siyah', 'Gri', 'Lacivert', 'Bordo'], 'Kadın' => ['Bordo', 'Gri', 'Beyaz', 'Pembe']], 'sort_order' => 3],
                ],
            ],
            [
                'name' => 'Kaban',
                'slug' => 'kaban',
                'category_slug' => 'kaban',
                'description' => 'Kışlık kaban. Kumaş ve cinsiyete göre renk.',
                'price' => 899.99,
                'sort_order' => 5,
                'stock_quantity' => 0,
                'variations' => [
                    ['name' => 'Kumaş', 'type' => 'select', 'depends_on' => null, 'options' => ['Yün', 'Polyester'], 'options_by_parent' => null, 'sort_order' => 1],
                    ['name' => 'Cinsiyet', 'type' => 'select', 'depends_on' => null, 'options' => ['Erkek', 'Kadın'], 'options_by_parent' => null, 'sort_order' => 2],
                    ['name' => 'Renk', 'type' => 'color', 'depends_on' => 'Cinsiyet', 'options' => [], 'options_by_parent' => ['Erkek' => ['Siyah', 'Camel', 'Lacivert', 'Gri'], 'Kadın' => ['Siyah', 'Bej', 'Bordo', 'Lacivert']], 'sort_order' => 3],
                ],
            ],
            [
                'name' => 'Klasik Pantolon',
                'slug' => 'klasik-pantolon',
                'category_slug' => 'pantolon',
                'description' => 'Ofis ve günlük pantolon. Tip ve cinsiyete göre beden.',
                'price' => 349.99,
                'sort_order' => 6,
                'stock_quantity' => 70,
                'variations' => [
                    ['name' => 'Tip', 'type' => 'select', 'depends_on' => null, 'options' => ['Klasik', 'Slim'], 'options_by_parent' => null, 'sort_order' => 1],
                    ['name' => 'Cinsiyet', 'type' => 'select', 'depends_on' => null, 'options' => ['Erkek', 'Kadın'], 'options_by_parent' => null, 'sort_order' => 2],
                    ['name' => 'Beden', 'type' => 'select', 'depends_on' => 'Cinsiyet', 'options' => [], 'options_by_parent' => ['Erkek' => ['42', '44', '46', '48'], 'Kadın' => ['36', '38', '40', '42']], 'sort_order' => 3],
                ],
            ],
            [
                'name' => 'Ceket',
                'slug' => 'ceket',
                'category_slug' => 'ceket',
                'description' => 'Spor veya klasik ceket. Kol ve cinsiyete göre renk.',
                'price' => 599.99,
                'sort_order' => 7,
                'stock_quantity' => 20,
                'variations' => [
                    ['name' => 'Kol', 'type' => 'select', 'depends_on' => null, 'options' => ['Kısa', 'Uzun'], 'options_by_parent' => null, 'sort_order' => 1],
                    ['name' => 'Cinsiyet', 'type' => 'select', 'depends_on' => null, 'options' => ['Erkek', 'Kadın'], 'options_by_parent' => null, 'sort_order' => 2],
                    ['name' => 'Renk', 'type' => 'color', 'depends_on' => 'Cinsiyet', 'options' => [], 'options_by_parent' => ['Erkek' => ['Siyah', 'Navy', 'Gri', 'Bej'], 'Kadın' => ['Siyah', 'Bej', 'Bordo', 'Lacivert']], 'sort_order' => 3],
                ],
            ],
            [
                'name' => 'Kazak',
                'slug' => 'kazak',
                'category_slug' => 'kazak',
                'description' => 'Düz veya örme desenli kazak. Cinsiyete göre renk.',
                'price' => 279.99,
                'sort_order' => 8,
                'stock_quantity' => 55,
                'variations' => [
                    ['name' => 'Desen', 'type' => 'select', 'depends_on' => null, 'options' => ['Düz', 'Örme'], 'options_by_parent' => null, 'sort_order' => 1],
                    ['name' => 'Cinsiyet', 'type' => 'select', 'depends_on' => null, 'options' => ['Erkek', 'Kadın'], 'options_by_parent' => null, 'sort_order' => 2],
                    ['name' => 'Renk', 'type' => 'color', 'depends_on' => 'Cinsiyet', 'options' => [], 'options_by_parent' => ['Erkek' => ['Lacivert', 'Bordo', 'Gri', 'Beyaz'], 'Kadın' => ['Pembe', 'Mor', 'Gri', 'Beyaz']], 'sort_order' => 3],
                ],
            ],
            [
                'name' => 'Kışlık Mont',
                'slug' => 'kislik-mont',
                'category_slug' => 'mont',
                'description' => 'İç astarlı veya astarsız mont. Cinsiyete göre renk.',
                'price' => 749.99,
                'sort_order' => 9,
                'stock_quantity' => 15,
                'variations' => [
                    ['name' => 'İç astar', 'type' => 'select', 'depends_on' => null, 'options' => ['Var', 'Yok'], 'options_by_parent' => null, 'sort_order' => 1],
                    ['name' => 'Cinsiyet', 'type' => 'select', 'depends_on' => null, 'options' => ['Erkek', 'Kadın'], 'options_by_parent' => null, 'sort_order' => 2],
                    ['name' => 'Renk', 'type' => 'color', 'depends_on' => 'Cinsiyet', 'options' => [], 'options_by_parent' => ['Erkek' => ['Siyah', 'Yeşil', 'Lacivert', 'Gri'], 'Kadın' => ['Siyah', 'Mavi', 'Bordo', 'Bej']], 'sort_order' => 3],
                ],
            ],
            [
                'name' => 'Şort',
                'slug' => 'short',
                'category_slug' => 'short',
                'description' => 'Yazlık şort. Boy ve cinsiyete göre renk.',
                'price' => 199.99,
                'sort_order' => 10,
                'stock_quantity' => 90,
                'variations' => [
                    ['name' => 'Boy', 'type' => 'select', 'depends_on' => null, 'options' => ['Kısa', 'Bermuda'], 'options_by_parent' => null, 'sort_order' => 1],
                    ['name' => 'Cinsiyet', 'type' => 'select', 'depends_on' => null, 'options' => ['Erkek', 'Kadın'], 'options_by_parent' => null, 'sort_order' => 2],
                    ['name' => 'Renk', 'type' => 'color', 'depends_on' => 'Cinsiyet', 'options' => [], 'options_by_parent' => ['Erkek' => ['Lacivert', 'Bej', 'Siyah', 'Gri'], 'Kadın' => ['Beyaz', 'Pembe', 'Lacivert', 'Siyah']], 'sort_order' => 3],
                ],
            ],
            [
                'name' => 'Bluz',
                'slug' => 'bluz',
                'category_slug' => 'bluz',
                'description' => 'Kısa veya uzun kollu bluz. Bağımsız renk seçimi.',
                'price' => 229.99,
                'sort_order' => 11,
                'stock_quantity' => 40,
                'variations' => [
                    ['name' => 'Kol', 'type' => 'select', 'depends_on' => null, 'options' => ['Kısa', 'Uzun'], 'options_by_parent' => null, 'sort_order' => 1],
                    ['name' => 'Renk', 'type' => 'color', 'depends_on' => null, 'options' => ['Beyaz', 'Pembe', 'Mavi', 'Lacivert'], 'options_by_parent' => null, 'sort_order' => 2],
                ],
            ],
            [
                'name' => 'Elbise',
                'slug' => 'elbise',
                'category_slug' => 'elbise',
                'description' => 'Kolsuz, kısa veya uzun kollu elbise. Beden seçimi.',
                'price' => 399.99,
                'sort_order' => 12,
                'stock_quantity' => 35,
                'variations' => [
                    ['name' => 'Kol', 'type' => 'select', 'depends_on' => null, 'options' => ['Kolsuz', 'Kısa', 'Uzun'], 'options_by_parent' => null, 'sort_order' => 1],
                    ['name' => 'Beden', 'type' => 'select', 'depends_on' => null, 'options' => ['36', '38', '40', '42'], 'options_by_parent' => null, 'sort_order' => 2],
                ],
            ],
            [
                'name' => 'Kravat',
                'slug' => 'kravat',
                'category_slug' => 'kravat',
                'description' => 'Düz veya çizgili kravat. Renk seçimi.',
                'price' => 129.99,
                'sort_order' => 13,
                'stock_quantity' => null,
                'variations' => [
                    ['name' => 'Desen', 'type' => 'select', 'depends_on' => null, 'options' => ['Düz', 'Çizgili'], 'options_by_parent' => null, 'sort_order' => 1],
                    ['name' => 'Renk', 'type' => 'color', 'depends_on' => null, 'options' => ['Kırmızı', 'Lacivert', 'Siyah', 'Gri'], 'options_by_parent' => null, 'sort_order' => 2],
                ],
            ],
            [
                'name' => 'Atkı',
                'slug' => 'atki',
                'category_slug' => 'atki',
                'description' => 'Yün veya kaşmir atkı.',
                'price' => 179.99,
                'sort_order' => 14,
                'stock_quantity' => 120,
                'variations' => [
                    ['name' => 'Malzeme', 'type' => 'select', 'depends_on' => null, 'options' => ['Yün', 'Kaşmir'], 'options_by_parent' => null, 'sort_order' => 1],
                    ['name' => 'Renk', 'type' => 'color', 'depends_on' => null, 'options' => ['Bordo', 'Gri', 'Lacivert', 'Bej'], 'options_by_parent' => null, 'sort_order' => 2],
                ],
            ],
            [
                'name' => 'Triko Yelek',
                'slug' => 'triko-yelek',
                'category_slug' => 'triko-yelek',
                'description' => 'Triko yelek. Cinsiyete göre renk.',
                'price' => 319.99,
                'sort_order' => 15,
                'stock_quantity' => 0,
                'variations' => [
                    ['name' => 'Cinsiyet', 'type' => 'select', 'depends_on' => null, 'options' => ['Erkek', 'Kadın'], 'options_by_parent' => null, 'sort_order' => 1],
                    ['name' => 'Renk', 'type' => 'color', 'depends_on' => 'Cinsiyet', 'options' => [], 'options_by_parent' => ['Erkek' => ['Lacivert', 'Bordo', 'Yeşil'], 'Kadın' => ['Pembe', 'Mor', 'Bej']], 'sort_order' => 2],
                ],
            ],
            [
                'name' => 'Etek',
                'slug' => 'etek',
                'category_slug' => 'etek',
                'description' => 'Midi veya maxi etek.',
                'price' => 269.99,
                'sort_order' => 16,
                'stock_quantity' => 28,
                'variations' => [
                    ['name' => 'Boy', 'type' => 'select', 'depends_on' => null, 'options' => ['Midi', 'Maxi'], 'options_by_parent' => null, 'sort_order' => 1],
                    ['name' => 'Renk', 'type' => 'color', 'depends_on' => null, 'options' => ['Siyah', 'Lacivert', 'Gri', 'Bordo'], 'options_by_parent' => null, 'sort_order' => 2],
                ],
            ],
        ];

        foreach ($products as $data) {
            $categorySlug = $data['category_slug'] ?? null;
            unset($data['category_slug'], $data['variations']);

            $categoryId = $categorySlug ? Category::where('slug', $categorySlug)->value('id') : null;
            $currencyId = Currency::getDefault()?->id;
            $taxClassId = TaxClass::first()?->id;

            Product::updateOrCreate(
                ['slug' => $data['slug']],
                array_merge($data, [
                    'company_id' => $company->id,
                    'category_id' => $categoryId,
                    'currency_id' => $currencyId,
                    'tax_class_id' => $taxClassId,
                    'stock_quantity' => array_key_exists('stock_quantity', $data) ? $data['stock_quantity'] : 10,
                    'is_active' => true,
                ])
            );
        }
    }
}
