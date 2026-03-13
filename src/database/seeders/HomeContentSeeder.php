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
                'headline' => 'Toptan Tekstil Ürünlerinde Fırsatlar',
                'description' => 'Kaliteli kumaşlar, uygun fiyatlar. Havale ile güvenli alışveriş.',
                'button_text' => 'Alışverişe Başla',
                'button_url' => '/#urunler',
                'text_align' => 'left',
                'sort_order' => 0,
            ],
            [
                'title' => 'Güvenli Alışveriş',
                'headline' => 'Havale ile Ödeme Kolaylığı',
                'description' => 'Siparişinizi onayladıktan sonra havale bilgilerimiz ile güvenle ödeyin.',
                'button_text' => 'Sepete Git',
                'button_url' => '/sepet',
                'text_align' => 'right',
                'sort_order' => 1,
            ],
            [
                'title' => 'B2B Çözümler',
                'headline' => 'Şirketinize Özel Fiyatlar ve Toplu Sipariş',
                'description' => 'Giriş yaparak fiyatları görüntüleyin, sipariş verin. Müşteri paneli ile takip edin.',
                'button_text' => 'Müşteri Girişi',
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
