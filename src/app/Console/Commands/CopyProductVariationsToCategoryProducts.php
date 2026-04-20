<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariation;
use App\Models\ProductVariationOption;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Örnek: php artisan product:copy-variations-to-category 6 tisort
 * Kaynak üründeki tüm varyasyon ve seçenekleri, slug'ı verilen kategorideki diğer ürünlere kopyalar.
 */
class CopyProductVariationsToCategoryProducts extends Command
{
    protected $signature = 'product:copy-variations-to-category
                            {source_product_id : Kaynak ürün id (örn. 6)}
                            {category_slug : Kategori slug (örn. tisort)}
                            {--dry-run : Veritabanına yazmadan özet göster}
                            {--no-replace : Hedefte varyasyon varsa atla (varsayılan: mevcutları silip kopyala)}';

    protected $description = 'Kaynak üründeki varyasyon ve seçenekleri, aynı kategorideki diğer ürünlere kopyalar.';

    public function handle(): int
    {
        $sourceId = (int) $this->argument('source_product_id');
        $slug = (string) $this->argument('category_slug');
        $dryRun = (bool) $this->option('dry-run');
        $noReplace = (bool) $this->option('no-replace');

        $source = Product::with(['variations' => fn ($q) => $q->orderBy('sort_order'), 'variations.options' => fn ($q) => $q->orderBy('sort_order')])
            ->find($sourceId);

        if (! $source) {
            $this->error("Kaynak ürün bulunamadı: id={$sourceId}");

            return self::FAILURE;
        }

        if ($source->variations->isEmpty()) {
            $this->error("Kaynak ürünün (id={$sourceId}) varyasyonu yok.");

            return self::FAILURE;
        }

        $category = Category::query()->where('slug', $slug)->first();
        if (! $category) {
            $this->error("Kategori bulunamadı: slug={$slug}");

            return self::FAILURE;
        }

        $targets = Product::query()
            ->where('category_id', $category->id)
            ->where('id', '!=', $source->id)
            ->orderBy('id')
            ->get();

        if ($targets->isEmpty()) {
            $this->warn('Bu kategoride kopyalanacak başka ürün yok.');

            return self::SUCCESS;
        }

        $this->info("Kaynak: ürün #{$source->id} ({$source->name}), {$source->variations->count()} varyasyon");
        $this->info("Kategori: {$category->name} (slug={$slug}), hedef ürün sayısı: {$targets->count()}");
        if ($dryRun) {
            $this->warn('Dry-run: kayıt yapılmayacak.');
        }

        foreach ($targets as $target) {
            if ($noReplace && $target->variations()->exists()) {
                $this->line("  Atlandı (varyasyon var, --no-replace): ürün #{$target->id} {$target->name}");

                continue;
            }

            if ($dryRun) {
                $this->line("  [dry-run] Kopyalanacak: ürün #{$target->id} {$target->name}");

                continue;
            }

            try {
                DB::transaction(function () use ($source, $target, $noReplace): void {
                    if (! $noReplace) {
                        $target->variations()->delete();
                    }

                    $variationMap = [];
                    foreach ($source->variations->sortBy('sort_order') as $oldVar) {
                        $newVar = $target->variations()->create([
                            'name' => $oldVar->name,
                            'type' => $oldVar->type,
                            'depends_on' => $oldVar->depends_on,
                            'sort_order' => (int) $oldVar->sort_order,
                        ]);
                        $variationMap[$oldVar->id] = $newVar;
                    }

                    $allOptionIds = $source->variations->flatMap(fn (ProductVariation $v) => $v->options->pluck('id'))->all();
                    $remaining = ProductVariationOption::query()
                        ->whereIn('id', $allOptionIds)
                        ->get()
                        ->keyBy('id');

                    $optionIdMap = [];

                    while ($remaining->isNotEmpty()) {
                        $batch = $remaining->filter(function (ProductVariationOption $opt) use ($optionIdMap) {
                            if ($opt->parent_option_id === null) {
                                return true;
                            }

                            return isset($optionIdMap[(int) $opt->parent_option_id]);
                        });

                        if ($batch->isEmpty()) {
                            throw new \RuntimeException(
                                'Seçenek üst referansları çözülemedi (döngü veya eksik parent). Kalan id: '
                                . $remaining->keys()->implode(', ')
                            );
                        }

                        foreach ($batch as $oldOpt) {
                            $oldVarId = (int) $oldOpt->product_variation_id;
                            if (! isset($variationMap[$oldVarId])) {
                                continue;
                            }
                            $newVar = $variationMap[$oldVarId];

                            $newParentId = null;
                            if ($oldOpt->parent_option_id !== null) {
                                $newParentId = $optionIdMap[(int) $oldOpt->parent_option_id] ?? null;
                            }

                            $newParentIds = null;
                            $rawParentIds = $oldOpt->parent_option_ids;
                            if (is_array($rawParentIds) && $rawParentIds !== []) {
                                $mapped = [];
                                foreach ($rawParentIds as $pid) {
                                    $pid = (int) $pid;
                                    if (isset($optionIdMap[$pid])) {
                                        $mapped[] = $optionIdMap[$pid];
                                    }
                                }
                                $newParentIds = $mapped === [] ? null : $mapped;
                            }

                            $newOpt = ProductVariationOption::query()->create([
                                'product_variation_id' => $newVar->id,
                                'option_value' => $oldOpt->option_value,
                                'option_color' => $oldOpt->option_color,
                                'option_image' => $oldOpt->option_image,
                                'option_image_size' => $oldOpt->option_image_size,
                                'price_delta' => $oldOpt->price_delta,
                                'stock_quantity' => $oldOpt->stock_quantity,
                                'parent_option_id' => $newParentId,
                                'parent_option_ids' => $newParentIds,
                                'sort_order' => (int) $oldOpt->sort_order,
                            ]);

                            $optionIdMap[(int) $oldOpt->id] = (int) $newOpt->getKey();
                            $remaining->forget($oldOpt->id);
                        }
                    }
                });
                $this->info("  Tamam: ürün #{$target->id} {$target->name}");
            } catch (\Throwable $e) {
                $this->error("  Hata ürün #{$target->id}: {$e->getMessage()}");

                return self::FAILURE;
            }
        }

        $this->info('Bitti.');

        return self::SUCCESS;
    }
}
