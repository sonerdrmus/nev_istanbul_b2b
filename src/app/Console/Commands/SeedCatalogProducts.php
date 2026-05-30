<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Product;
use App\Support\ProductFromTemplateCloner;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

/**
 * Katalog ürünlerini şablon üründen (admin panel yapısı) toplu ekler.
 *
 * Örnek: php artisan product:seed-catalog
 */
class SeedCatalogProducts extends Command
{
    protected $signature = 'product:seed-catalog
                            {--template=6 : Şablon ürün id}
                            {--dry-run : Veritabanına yazmadan listele}';

    protected $description = 'Katalog ürünlerini şablon ürün varyasyonlarıyla oluşturur.';

    /** @var list<array{name: string, category_slug: string}> */
    private const PRODUCTS = [
        ['name' => 'Apron', 'category_slug' => 'aprons'],
        ['name' => 'Backpack', 'category_slug' => 'bags'],
        ['name' => 'Bandana', 'category_slug' => 'hats'],
        ['name' => 'Baseball Hat', 'category_slug' => 'hats'],
        ['name' => 'Beach Towel', 'category_slug' => 'towels'],
        ['name' => 'Boiler Suit', 'category_slug' => 'is-giyim'],
        ['name' => 'Bucket Hat', 'category_slug' => 'hats'],
        ['name' => 'Fleece Scarf', 'category_slug' => 'tekstil-aksesuarlari'],
        ['name' => 'Lab Coat', 'category_slug' => 'is-giyim'],
        ['name' => 'Napkins', 'category_slug' => 'ev-tekstili'],
        ['name' => 'Pillow', 'category_slug' => 'ev-tekstili'],
        ['name' => 'Puffer Vest', 'category_slug' => 'is-giyim'],
        ['name' => 'Runner', 'category_slug' => 'ev-tekstili'],
        ['name' => 'Shirt', 'category_slug' => 'is-giyim'],
        ['name' => 'Socks', 'category_slug' => 'socks'],
        ['name' => 'Sweater', 'category_slug' => 'is-giyim'],
        ['name' => 'Sweatshirt', 'category_slug' => 'tisort'],
        ['name' => 'Tablecloth', 'category_slug' => 'ev-tekstili'],
        ['name' => 'Team Jersey', 'category_slug' => 'is-giyim'],
        ['name' => 'Tote Bag', 'category_slug' => 'bags'],
        ['name' => 'Trucker Hat', 'category_slug' => 'hats'],
        ['name' => 'Winter Hats', 'category_slug' => 'hats'],
        ['name' => 'Workwear Bib', 'category_slug' => 'is-giyim'],
        ['name' => 'Workwear Jacket', 'category_slug' => 'is-giyim'],
    ];

    public function handle(ProductFromTemplateCloner $cloner): int
    {
        $templateId = (int) $this->option('template');
        $dryRun = (bool) $this->option('dry-run');

        $template = Product::query()->with('variations.options')->find($templateId);
        if ($template === null) {
            $this->error("Şablon ürün bulunamadı: #{$templateId}");

            return self::FAILURE;
        }

        $this->info("Şablon: {$template->name} (#{$template->getKey()}) — {$template->variations->count()} varyasyon");

        $nextSortOrder = (int) (Product::query()->max('sort_order') ?? 0);
        $nextShowcaseOrder = (int) (Product::query()->where('show_on_home', true)->max('home_showcase_order') ?? 0);
        $created = 0;
        $skipped = 0;

        foreach (self::PRODUCTS as $item) {
            $slug = Str::slug($item['name']);
            $categoryId = Category::query()->where('slug', $item['category_slug'])->value('id');

            if ($categoryId === null) {
                $this->warn("Atlandı (kategori yok: {$item['category_slug']}): {$item['name']}");
                $skipped++;

                continue;
            }

            if (Product::query()->where('slug', $slug)->exists()) {
                $this->line("Zaten var: {$item['name']} ({$slug})");
                $skipped++;

                continue;
            }

            if ($dryRun) {
                $this->line("[dry-run] Eklenecek: {$item['name']} → {$item['category_slug']}");
                $created++;

                continue;
            }

            $nextSortOrder++;
            $nextShowcaseOrder++;

            $product = $cloner->clone($template, [
                'name' => $item['name'],
                'slug' => $slug,
                'category_id' => $categoryId,
                'description' => $item['name'].' ürün sayfası.',
                'sort_order' => $nextSortOrder,
                'show_on_home' => true,
                'home_showcase_order' => $nextShowcaseOrder,
            ]);

            $optionCount = $product->variations->sum(fn ($variation) => $variation->options->count());
            $this->line("Eklendi: {$product->name} (#{$product->getKey()}) — {$product->variations->count()} varyasyon, {$optionCount} seçenek");
            $created++;
        }

        if ($dryRun) {
            $this->info("Dry-run: {$created} ürün eklenecek, {$skipped} atlandı.");
        } else {
            $this->info("Tamamlandı: {$created} ürün eklendi, {$skipped} atlandı.");
        }

        return self::SUCCESS;
    }
}
