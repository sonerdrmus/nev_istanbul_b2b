<?php

use App\Models\FooterMenuGroup;
use App\Models\FooterMenuItem;
use App\Models\LegalPage;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        /** @var list<array{slug: string, sort_order: int, footer_label: string, title: string, title_en: string, title_it: string, body: string, body_en: string, body_it: string, overwrite_body: bool}> $pages */
        $pages = require database_path('seeders/data/legal_footer_pages.php');
        $page = collect($pages)->firstWhere('slug', LegalPage::KVKK_NOTICE_SLUG);
        if (! is_array($page)) {
            return;
        }

        LegalPage::updateOrCreate(
            ['slug' => $page['slug']],
            [
                'title' => $page['title'],
                'title_en' => $page['title_en'],
                'title_it' => $page['title_it'],
                'body' => LegalPage::inlineBareClauseNumbers($page['body']),
                'body_en' => LegalPage::inlineBareClauseNumbers($page['body_en']),
                'body_it' => LegalPage::inlineBareClauseNumbers($page['body_it']),
                'is_published' => true,
                'sort_order' => $page['sort_order'],
            ]
        );

        $dataProtection = collect($pages)->firstWhere('slug', LegalPage::DATA_PROTECTION_SLUG);
        if (is_array($dataProtection)) {
            LegalPage::query()->where('slug', LegalPage::DATA_PROTECTION_SLUG)->update([
                'body' => LegalPage::inlineBareClauseNumbers($dataProtection['body']),
                'body_en' => LegalPage::inlineBareClauseNumbers($dataProtection['body_en']),
                'body_it' => LegalPage::inlineBareClauseNumbers($dataProtection['body_it']),
            ]);
        }

        $group = FooterMenuGroup::query()
            ->whereIn('title', ['Sözleşmeler', 'Legal', 'Documenti legali'])
            ->orderBy('id')
            ->first();

        if (! $group) {
            return;
        }

        $exists = FooterMenuItem::query()
            ->where('footer_menu_group_id', $group->id)
            ->where(function ($q) use ($page): void {
                $q->where('label', $page['footer_label'])
                    ->orWhere('url', '/sozlesme/'.$page['slug']);
            })
            ->exists();

        if (! $exists) {
            $maxSort = (int) FooterMenuItem::query()
                ->where('footer_menu_group_id', $group->id)
                ->max('sort_order');

            FooterMenuItem::create([
                'footer_menu_group_id' => $group->id,
                'label' => $page['footer_label'],
                'label_en' => $page['title_en'],
                'label_it' => $page['title_it'],
                'url' => '/sozlesme/'.$page['slug'],
                'open_in_new_tab' => false,
                'sort_order' => $maxSort + 1,
            ]);
        }
    }

    public function down(): void
    {
        FooterMenuItem::query()
            ->where('url', '/sozlesme/'.LegalPage::KVKK_NOTICE_SLUG)
            ->delete();

        LegalPage::query()
            ->where('slug', LegalPage::KVKK_NOTICE_SLUG)
            ->delete();
    }
};
