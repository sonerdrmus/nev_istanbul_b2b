<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class EnsureProductSampleImages extends Command
{
    protected $signature = 'storage:ensure-product-sample-images';

    protected $description = 'DB\'de products/samples/... yolu olan ama diski olmayan örnek SVG görsellerini oluşturur';

    public function handle(): int
    {
        $prefix = 'products/samples/';

        $products = Product::query()
            ->where('image', 'like', $prefix.'%')
            ->get(['id', 'name', 'image']);

        if ($products->isEmpty()) {
            $this->info('Örnek yol (products/samples/...) kullanan ürün yok.');

            return self::SUCCESS;
        }

        $created = 0;
        foreach ($products as $product) {
            $relative = $product->image;
            if (! is_string($relative) || ! str_starts_with($relative, $prefix)) {
                continue;
            }

            $absolute = storage_path('app/public/'.$relative);
            if (File::exists($absolute)) {
                continue;
            }

            File::ensureDirectoryExists(dirname($absolute));
            File::put($absolute, $this->buildSampleSvg((string) $product->name));
            $created++;
            $this->line("Oluşturuldu: {$relative}");
        }

        $this->info($created === 0
            ? 'Eksik dosya yok (hepsi zaten mevcut).'
            : "Toplam {$created} dosya yazıldı.");

        return self::SUCCESS;
    }

    private function buildSampleSvg(string $title): string
    {
        $safeTitle = e($title);

        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="1200" height="1200" viewBox="0 0 1200 1200">
  <defs>
    <linearGradient id="bg" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0%" stop-color="#0f4c81"/>
      <stop offset="100%" stop-color="#2a7ab9"/>
    </linearGradient>
  </defs>
  <rect width="1200" height="1200" fill="url(#bg)"/>
  <rect x="110" y="110" width="980" height="980" rx="42" fill="rgba(255,255,255,0.12)"/>
  <text x="600" y="560" text-anchor="middle" font-size="76" font-family="Arial, sans-serif" fill="#ffffff" font-weight="700">{$safeTitle}</text>
  <text x="600" y="650" text-anchor="middle" font-size="34" font-family="Arial, sans-serif" fill="rgba(255,255,255,0.85)">Örnek Ürün Görseli</text>
</svg>
SVG;
    }
}
