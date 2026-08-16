<?php

namespace Database\Seeders;

use App\Models\BannerSlide;
use App\Models\HomeSection;
use Illuminate\Database\Seeder;

class HomeContentSeeder extends Seeder
{
    public function run(): void
    {
        $banners = [
            [
                'title' => 'Yeni Sezon',
                'title_en' => 'New Season',
                'title_it' => 'Nuova stagione',
                'headline' => 'Toptan Tekstil Ürünlerinde Fırsatlar',
                'headline_en' => 'Wholesale textile offers',
                'headline_it' => 'Opportunità nel tessile all’ingrosso',
                'description' => 'Kaliteli kumaşlar, uygun fiyatlar. Havale ile güvenli alışveriş.',
                'description_en' => 'Quality fabrics, fair prices. Secure payment by bank transfer.',
                'description_it' => 'Tessuti di qualità, prezzi vantaggiosi. Pagamento sicuro tramite bonifico.',
                'button_text' => 'Alışverişe Başla',
                'button_text_en' => 'Start shopping',
                'button_text_it' => 'Inizia lo shopping',
                'button_url' => '/#urunler',
                'text_align' => 'left',
                'sort_order' => 0,
            ],
            [
                'title' => 'Güvenli Alışveriş',
                'title_en' => 'Secure shopping',
                'title_it' => 'Acquisti sicuri',
                'headline' => 'Havale ile Ödeme Kolaylığı',
                'headline_en' => 'Easy payment by bank transfer',
                'headline_it' => 'Pagamento facile tramite bonifico',
                'description' => 'Siparişinizi onayladıktan sonra havale bilgilerimiz ile güvenle ödeyin.',
                'description_en' => 'After confirming your order, pay securely using our bank transfer details.',
                'description_it' => 'Dopo aver confermato l’ordine, paga in sicurezza con i nostri dati di bonifico.',
                'button_text' => 'Sepete Git',
                'button_text_en' => 'Go to cart',
                'button_text_it' => 'Vai al carrello',
                'button_url' => '/sepet',
                'text_align' => 'right',
                'sort_order' => 1,
            ],
            [
                'title' => 'B2B Çözümler',
                'title_en' => 'B2B solutions',
                'title_it' => 'Soluzioni B2B',
                'headline' => 'Şirketinize Özel Fiyatlar ve Toplu Sipariş',
                'headline_en' => 'Company pricing and bulk orders',
                'headline_it' => 'Prezzi aziendali e ordini all’ingrosso',
                'description' => 'Giriş yaparak fiyatları görüntüleyin, sipariş verin. Müşteri paneli ile takip edin.',
                'description_en' => 'Sign in to view prices, place orders, and track them in the customer panel.',
                'description_it' => 'Accedi per vedere i prezzi, ordinare e seguire tutto dal pannello cliente.',
                'button_text' => 'Müşteri Girişi',
                'button_text_en' => 'Customer login',
                'button_text_it' => 'Accesso clienti',
                'button_url' => '/panel/login',
                'text_align' => 'center',
                'sort_order' => 2,
            ],
        ];

        foreach ($banners as $data) {
            BannerSlide::updateOrCreate(
                ['title' => $data['title']],
                array_merge($data, ['is_active' => true])
            );
        }

        $sections = [
            [
                'label' => 'Kampanya',
                'title' => 'Üst Giyim',
                'subtitle' => 'Tişört, gömlek ve sweatshirt modelleri',
                'button_text' => 'İncele',
                'link_url' => '/?category=tisort',
                'sort_order' => 0,
            ],
            [
                'label' => 'Koleksiyon',
                'title' => 'Alt Giyim',
                'subtitle' => 'Pantolon, şort ve etek',
                'button_text' => 'İncele',
                'link_url' => '/?category=pantolon',
                'sort_order' => 1,
            ],
        ];

        foreach ($sections as $data) {
            HomeSection::updateOrCreate(
                ['title' => $data['title'], 'label' => $data['label']],
                array_merge($data, ['is_active' => true])
            );
        }
    }
}
