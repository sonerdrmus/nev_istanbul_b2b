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
        $this->seedLegalFooterPages();
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

    public function seedLegalFooterPages(): void
    {
        /** @var list<array{slug: string, sort_order: int, footer_label: string, title: string, title_en: string, title_it: string, body: string, body_en: string, body_it: string, overwrite_body: bool}> $pages */
        $pages = require database_path('seeders/data/legal_footer_pages.php');

        foreach ($pages as $page) {
            $existing = LegalPage::query()->where('slug', $page['slug'])->first();
            if ($existing) {
                $keep = [
                    'is_published' => true,
                    'sort_order' => $page['sort_order'],
                ];
                if (blank($existing->title_en)) {
                    $keep['title_en'] = $page['title_en'];
                }
                if (blank($existing->title_it)) {
                    $keep['title_it'] = $page['title_it'];
                }
                if ($page['body'] !== '' && $page['overwrite_body'] && blank($existing->body)) {
                    $keep['body'] = LegalPage::inlineBareClauseNumbers($page['body']);
                    $keep['body_en'] = LegalPage::inlineBareClauseNumbers($page['body_en']);
                    $keep['body_it'] = LegalPage::inlineBareClauseNumbers($page['body_it']);
                }
                $existing->update($keep);

                continue;
            }

            $create = [
                'slug' => $page['slug'],
                'title' => $page['title'],
                'title_en' => $page['title_en'],
                'title_it' => $page['title_it'],
                'is_published' => true,
                'sort_order' => $page['sort_order'],
            ];
            if ($page['body'] !== '') {
                $create['body'] = LegalPage::inlineBareClauseNumbers($page['body']);
                $create['body_en'] = LegalPage::inlineBareClauseNumbers($page['body_en']);
                $create['body_it'] = LegalPage::inlineBareClauseNumbers($page['body_it']);
            }

            LegalPage::create($create);
        }

        $group = \App\Models\FooterMenuGroup::query()
            ->whereIn('title', ['Sözleşmeler', 'Legal', 'Documenti legali'])
            ->orderBy('id')
            ->first();

        if (! $group) {
            $group = \App\Models\FooterMenuGroup::create([
                'title' => 'Sözleşmeler',
                'type' => \App\Models\FooterMenuGroup::TYPE_MENU,
                'sort_order' => 30,
            ]);
        }

        \App\Models\FooterMenuItem::query()
            ->where('footer_menu_group_id', $group->id)
            ->delete();

        foreach (array_values($pages) as $index => $page) {
            \App\Models\FooterMenuItem::create([
                'footer_menu_group_id' => $group->id,
                'label' => $page['footer_label'],
                'url' => '/sozlesme/'.$page['slug'],
                'open_in_new_tab' => false,
                'sort_order' => $index + 1,
            ]);
        }
    }

    private function html(string $prefix, string $locale): string
    {
        $path = database_path('seeders/data/legal_pages/'.$prefix.'.'.$locale.'.html');

        return LegalPage::inlineBareClauseNumbers((string) file_get_contents($path));
    }
}
