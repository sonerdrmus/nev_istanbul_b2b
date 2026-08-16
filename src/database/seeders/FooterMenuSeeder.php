<?php

namespace Database\Seeders;

use App\Models\FooterMenuGroup;
use App\Models\FooterMenuItem;
use App\Models\FooterSetting;
use Illuminate\Database\Seeder;

class FooterMenuSeeder extends Seeder
{
    /**
     * Varsayılan footer ayarları, menü grupları ve linkleri. Admin panelinden düzenlenebilir.
     */
    public function run(): void
    {
        FooterSetting::firstOrCreate([], [
            'columns' => 4,
            'show_brand' => true,
        ]);

        $groups = [
            [
                'title' => 'Kategoriler',
                'type' => FooterMenuGroup::TYPE_CATEGORIES,
                'sort_order' => 5,
                'items' => [],
            ],
            [
                'title' => 'Müşteri Hizmetleri',
                'type' => FooterMenuGroup::TYPE_MENU,
                'sort_order' => 10,
                'items' => [
                    ['label' => 'Tüm Ürünler', 'url' => null, 'sort_order' => 1],
                    ['label' => 'Sepet', 'url' => null, 'sort_order' => 2],
                    ['label' => 'Hesabım', 'url' => null, 'sort_order' => 3],
                ],
            ],
            [
                'title' => 'Teslimat & Ödeme',
                'type' => FooterMenuGroup::TYPE_MENU,
                'sort_order' => 20,
                'items' => [
                    ['label' => 'Teslimat bilgisi ve maliyetler', 'url' => '#', 'sort_order' => 1],
                    ['label' => 'Faturasız ödeme', 'url' => '#', 'sort_order' => 2],
                    ['label' => 'İade', 'url' => '#', 'sort_order' => 3],
                    ['label' => 'Worldwide B2B Delivery', 'url' => '#', 'sort_order' => 4],
                ],
            ],
            [
                'title' => 'Sözleşmeler',
                'type' => FooterMenuGroup::TYPE_MENU,
                'sort_order' => 30,
                'items' => [
                    ['label' => 'Kullanım Koşulları', 'url' => '/sozlesme/kullanim-kosullari', 'sort_order' => 1],
                    ['label' => 'Gizlilik Politikası', 'url' => '/sozlesme/gizlilik-politikasi', 'sort_order' => 2],
                    ['label' => 'Mesafeli Satış Sözleşmesi', 'url' => '#', 'sort_order' => 3],
                    ['label' => 'Çerez Politikası', 'url' => '#', 'sort_order' => 4],
                ],
            ],
            [
                'title' => 'Şirket',
                'type' => FooterMenuGroup::TYPE_MENU,
                'sort_order' => 40,
                'items' => [
                    ['label' => 'İletişim', 'url' => '/iletisim', 'sort_order' => 1],
                    ['label' => 'Gizlilik', 'url' => '/sozlesme/gizlilik-politikasi', 'sort_order' => 2],
                ],
            ],
            [
                'title' => 'Banka Bilgileri',
                'type' => FooterMenuGroup::TYPE_BANK_INFO,
                'sort_order' => 50,
                'items' => [],
            ],
        ];

        $defaultUrls = [
            'Tüm Ürünler' => route('home'),
            'Sepet' => route('store.cart'),
            'Hesabım' => url('/panel'),
            'Tümünü gör' => route('home'),
        ];

        foreach ($groups as $groupData) {
            $itemsData = $groupData['items'];
            $type = $groupData['type'];
            unset($groupData['items'], $groupData['type']);

            $group = FooterMenuGroup::firstOrCreate(
                ['title' => $groupData['title']],
                ['sort_order' => $groupData['sort_order'], 'type' => $type]
            );
            $group->update(['sort_order' => $groupData['sort_order'], 'type' => $type]);

            foreach ($itemsData as $itemData) {
                $url = $itemData['url'] ?? ($defaultUrls[$itemData['label']] ?? '#');
                FooterMenuItem::firstOrCreate(
                    [
                        'footer_menu_group_id' => $group->id,
                        'label' => $itemData['label'],
                    ],
                    [
                        'url' => $url,
                        'open_in_new_tab' => false,
                        'sort_order' => $itemData['sort_order'],
                    ]
                );
            }
        }
    }
}
