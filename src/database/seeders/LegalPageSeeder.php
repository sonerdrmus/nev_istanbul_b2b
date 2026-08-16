<?php

namespace Database\Seeders;

use App\Models\FooterMenuItem;
use App\Models\LegalPage;
use Illuminate\Database\Seeder;

class LegalPageSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedPage(LegalPage::PRIVACY_SLUG, [
            'title' => 'Gizlilik ve Kişisel Verilerin Korunması Politikası',
            'title_en' => 'Privacy and Personal Data Protection Policy',
            'title_it' => 'Informativa sulla privacy e sulla protezione dei dati personali',
            'prefix' => 'gizlilik-politikasi',
            'sort_order' => 10,
            'footer_labels' => [
                'Gizlilik Politikası',
                'Gizlilik',
                'Privacy Policy',
                'Privacy',
                'Informativa sulla privacy',
            ],
        ]);

        $this->seedPage(LegalPage::TERMS_SLUG, [
            'title' => 'B2B Web Sitesi Kullanım Koşulları',
            'title_en' => 'B2B Website Terms & Conditions',
            'title_it' => 'Termini e condizioni del sito B2B',
            'prefix' => 'kullanim-kosullari',
            'sort_order' => 20,
            'footer_labels' => [
                'Kullanım Koşulları',
                'Terms & Conditions',
                'Terms and Conditions',
                'Termini e condizioni',
            ],
        ]);

        $this->seedContact();
    }

    public function seedContact(): void
    {
        $this->seedPage(LegalPage::CONTACT_SLUG, [
            'title' => 'İletişim',
            'title_en' => 'Contact',
            'title_it' => 'Contatti',
            'prefix' => 'iletisim',
            'sort_order' => 30,
            'url' => '/iletisim',
            'footer_labels' => [
                'İletişim',
                'Contact',
                'Contatti',
            ],
        ]);
    }

    /**
     * @param  array{title: string, title_en: string, title_it: string, prefix: string, sort_order: int, footer_labels: list<string>, url?: string}  $meta
     */
    private function seedPage(string $slug, array $meta): void
    {
        LegalPage::updateOrCreate(
            ['slug' => $slug],
            [
                'title' => $meta['title'],
                'title_en' => $meta['title_en'],
                'title_it' => $meta['title_it'],
                'body' => $this->html($meta['prefix'], 'tr'),
                'body_en' => $this->html($meta['prefix'], 'en'),
                'body_it' => $this->html($meta['prefix'], 'it'),
                'is_published' => true,
                'sort_order' => $meta['sort_order'],
            ]
        );

        FooterMenuItem::query()
            ->whereIn('label', $meta['footer_labels'])
            ->update(['url' => $meta['url'] ?? '/sozlesme/'.$slug]);
    }

    private function html(string $prefix, string $locale): string
    {
        $path = database_path('seeders/data/legal_pages/'.$prefix.'.'.$locale.'.html');

        return LegalPage::inlineBareClauseNumbers((string) file_get_contents($path));
    }
}
