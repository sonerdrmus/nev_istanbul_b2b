<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Product;
use App\Support\MachineTranslator;
use Illuminate\Console\Command;

class FillEnglishCatalogTranslations extends Command
{
    protected $signature = 'catalog:fill-english
                            {--force : Overwrite existing English fields}
                            {--only=products : products|categories|labels|all}';

    protected $description = 'Auto-translate empty product/category English fields and fill EN/IT catalog names';

    public function handle(): int
    {
        $force = (bool) $this->option('force');
        $only = (string) $this->option('only');

        if ($only === 'all' || $only === 'products') {
            $this->fillProducts($force);
        }
        if ($only === 'all' || $only === 'categories') {
            $this->fillCategories($force);
        }

        if ($only === 'all' || $only === 'labels') {
            $n = \App\Support\CatalogLocaleBackfill::all();
            $this->info("Catalog names filled: {$n} rows");
        }

        return self::SUCCESS;
    }

    private function fillProducts(bool $force): void
    {
        $query = Product::query()->orderBy('id');
        $total = (clone $query)->count();
        $this->info("Products: {$total}");
        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $query->chunkById(25, function ($products) use ($force, $bar): void {
            foreach ($products as $product) {
                /** @var Product $product */
                $dirty = false;
                if (filled($product->name) && ($force || blank($product->name_en))) {
                    $t = MachineTranslator::translate((string) $product->name, 'tr', 'en');
                    if (filled($t)) {
                        $product->name_en = $t;
                        $dirty = true;
                    }
                }
                if (filled($product->description) && ($force || blank($product->description_en))) {
                    $t = MachineTranslator::translateHtml((string) $product->description, 'tr', 'en');
                    if (filled($t)) {
                        $product->description_en = $t;
                        $dirty = true;
                    }
                }
                if (filled($product->meta_title) && ($force || blank($product->meta_title_en))) {
                    $t = MachineTranslator::translate((string) $product->meta_title, 'tr', 'en');
                    if (filled($t)) {
                        $product->meta_title_en = $t;
                        $dirty = true;
                    }
                }
                if (filled($product->meta_description) && ($force || blank($product->meta_description_en))) {
                    $t = MachineTranslator::translate((string) $product->meta_description, 'tr', 'en');
                    if (filled($t)) {
                        $product->meta_description_en = $t;
                        $dirty = true;
                    }
                }
                if ($dirty) {
                    // Avoid recursive machine translation in saving hook when already filled
                    $product->saveQuietly();
                }
                $bar->advance();
                usleep(150000); // gentle rate limit
            }
        });

        $bar->finish();
        $this->newLine();
    }

    private function fillCategories(bool $force): void
    {
        $query = Category::query()->orderBy('id');
        $total = (clone $query)->count();
        $this->info("Categories: {$total}");
        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $query->chunkById(50, function ($categories) use ($force, $bar): void {
            foreach ($categories as $category) {
                /** @var Category $category */
                if (filled($category->name) && ($force || blank($category->name_en))) {
                    $t = MachineTranslator::translate((string) $category->name, 'tr', 'en');
                    if (filled($t)) {
                        $category->name_en = $t;
                        $category->saveQuietly();
                    }
                }
                $bar->advance();
                usleep(100000);
            }
        });

        $bar->finish();
        $this->newLine();
    }
}
