<?php

namespace App\Console\Commands;

use App\Filament\Resources\ProductResource;
use App\Models\ProductVariation;
use Illuminate\Console\Command;

/**
 * Tüm ürünlerdeki "Renk" varyasyon seçeneklerini arayüz renk kayıtlarıyla senkronize eder.
 *
 * Örnek: php artisan product:sync-color-variation-options
 */
class SyncProductColorVariationOptions extends Command
{
    protected $signature = 'product:sync-color-variation-options
                            {--product= : Yalnızca belirtilen ürün id}
                            {--dry-run : Veritabanına yazmadan özet göster}';

    protected $description = 'Renk varyasyonu seçeneklerini Arayüz renk kayıtlarına bağlar (kumaş türüne göre mağaza filtresi için).';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $productId = $this->option('product') !== null ? (int) $this->option('product') : null;

        $query = ProductVariation::query()
            ->where('type', 'color')
            ->with('product:id,name')
            ->orderBy('product_id')
            ->orderBy('sort_order');

        if ($productId) {
            $query->where('product_id', $productId);
        }

        $variations = $query->get();

        if ($variations->isEmpty()) {
            $this->warn('Senkronize edilecek "Renk" tipi varyasyon yok.');

            return self::SUCCESS;
        }

        $presetCount = count(ProductResource::colorVariationOptionsFromInterfacePresets());
        if ($presetCount === 0) {
            $this->error('Arayüzde aktif renk kaydı yok. Önce Renk varyasyonları panelinden kayıt ekleyin.');

            return self::FAILURE;
        }

        $this->info("Arayüz renk preset sayısı: {$presetCount}");
        $totalOptions = 0;

        foreach ($variations as $variation) {
            $productName = $variation->product?->name ?? ('#'.$variation->product_id);
            $oldCount = $variation->options()->count();

            if ($dryRun) {
                $this->line("[dry-run] Ürün {$variation->product_id} ({$productName}) — {$variation->name}: {$oldCount} seçenek → {$presetCount} seçenek");

                continue;
            }

            $created = ProductResource::syncColorVariationOptionsForVariation($variation);
            $totalOptions += $created;
            $this->line("Ürün {$variation->product_id} ({$productName}) — {$variation->name}: {$oldCount} → {$created} seçenek");
        }

        if ($dryRun) {
            $this->info("Dry-run: {$variations->count()} renk varyasyonu güncellenecek.");
        } else {
            $this->info("Tamamlandı: {$variations->count()} varyasyon, toplam {$totalOptions} seçenek yazıldı.");
        }

        return self::SUCCESS;
    }
}
