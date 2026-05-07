<?php

namespace App\Console\Commands;

use App\Models\Currency;
use App\Services\TcmbExchangeRateService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class UpdateExchangeRates extends Command
{
    protected $signature = 'currency:update-rates';

    protected $description = 'TCMB\'den döviz kurlarını çekip günceller';

    public function handle(TcmbExchangeRateService $tcmb): int
    {
        $this->info('TCMB döviz kurları güncelleniyor...');

        try {
            $rates = $tcmb->fetchFromTcmbTodayXml(['USD', 'EUR']);
            if ($rates === []) {
                $this->error('TCMB\'den USD/EUR kuru alınamadı.');
                return Command::FAILURE;
            }

            // USD ve EUR güncelle
            $updated = 0;
            if (isset($rates['USD'])) {
                $usd = Currency::where('code', 'USD')->first();
                if ($usd) {
                    $usd->update(['exchange_rate' => $rates['USD']]);
                    $this->info("USD: {$rates['USD']} TL");
                    $updated++;
                }
            }

            if (isset($rates['EUR'])) {
                $eur = Currency::where('code', 'EUR')->first();
                if ($eur) {
                    $eur->update(['exchange_rate' => $rates['EUR']]);
                    $this->info("EUR: {$rates['EUR']} TL");
                    $updated++;
                }
            }

            // TRY için exchange_rate = 1.0 (varsayılan)
            $try = Currency::where('code', 'TRY')->first();
            if ($try && $try->exchange_rate != 1.0) {
                $try->update(['exchange_rate' => 1.0]);
            }

            $tcmb->bustCache();

            if ($updated > 0) {
                $this->info("✓ {$updated} para birimi güncellendi.");
                return Command::SUCCESS;
            }

            $this->warn('USD/EUR veritabanında bulunamadı; kurlar dosyadan okundu, önbellek sıfırlandı.');
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Hata: ' . $e->getMessage());
            Log::error('Exchange rate update failed', ['error' => $e->getMessage()]);
            return Command::FAILURE;
        }
    }
}
