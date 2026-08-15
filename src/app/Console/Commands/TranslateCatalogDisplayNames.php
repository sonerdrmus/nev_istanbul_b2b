<?php

namespace App\Console\Commands;

use App\Support\CatalogLocaleBackfill;
use Illuminate\Console\Command;

class TranslateCatalogDisplayNames extends Command
{
    protected $signature = 'catalog:translate-display-names';

    protected $description = 'Translate leftover catalog display names into EN and IT without changing matching keys';

    public function handle(): int
    {
        $this->info('Çeviri başlıyor (eşleştirme alanları değişmez).');

        $result = CatalogLocaleBackfill::machineTranslateRemaining(function (string $source, string $en, string $it): void {
            $this->line(sprintf('• %s  →  EN: %s  |  IT: %s', $source, $en, $it));
        });

        $this->info("Benzersiz metin: {$result['unique']}");
        $this->info("Güncellenen satır: {$result['updated']}");

        return self::SUCCESS;
    }
}
