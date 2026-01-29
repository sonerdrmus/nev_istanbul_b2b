<?php

namespace App\Console\Commands;

use App\Models\Currency;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UpdateExchangeRates extends Command
{
    protected $signature = 'currency:update-rates';

    protected $description = 'TCMB\'den döviz kurlarını çekip günceller';

    public function handle(): int
    {
        $this->info('TCMB döviz kurları güncelleniyor...');

        try {
            $response = Http::timeout(10)->get('https://www.tcmb.gov.tr/kurlar/today.xml');

            if (! $response->successful()) {
                $this->error('TCMB API\'sine erişilemedi.');
                return Command::FAILURE;
            }

            $xml = simplexml_load_string($response->body());
            if (! $xml) {
                $this->error('XML parse edilemedi.');
                return Command::FAILURE;
            }

            $rates = [];
            foreach ($xml->Currency as $currency) {
                $code = (string) $currency['Kod'];
                $forexBuying = (string) $currency->ForexBuying;
                if ($code && $forexBuying && $forexBuying !== '') {
                    $rates[$code] = (float) $forexBuying;
                }
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

            if ($updated > 0) {
                $this->info("✓ {$updated} para birimi güncellendi.");
                return Command::SUCCESS;
            } else {
                $this->warn('Güncellenecek kur bulunamadı.');
                return Command::SUCCESS;
            }
        } catch (\Exception $e) {
            $this->error('Hata: ' . $e->getMessage());
            Log::error('Exchange rate update failed', ['error' => $e->getMessage()]);
            return Command::FAILURE;
        }
    }
}
