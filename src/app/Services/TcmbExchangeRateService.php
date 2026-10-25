<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;

class TcmbExchangeRateService
{
    /** Kur: 1 USD/EUR için kaç TL (TCMB Döviz Alış / ForexBuying) */
    public function fetchFromTcmbTodayXml(?array $onlyCodes = null): array
    {
        $onlyCodes ??= ['USD', 'EUR'];
        $wanted = array_fill_keys(array_map('strtoupper', $onlyCodes), true);

        try {
            $url = config('services.tcmb.kurlar_url');
            $response = Http::timeout((int) config('services.tcmb.timeout', 12))
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (compatible; B2B-Store-Tcmb/1.0)',
                    'Accept' => 'application/xml, text/xml, */*',
                ])
                ->get($url);

            if (! $response->successful()) {
                Log::warning('tcmb.exchange.fetch_http', [
                    'status' => $response->status(),
                    'body_snip' => substr($response->body(), 0, 200),
                ]);

                return [];
            }

            libxml_use_internal_errors(true);
            $xml = simplexml_load_string($response->body());
            if ($xml === false) {
                Log::warning('tcmb.exchange.xml_parse_failed');

                return [];
            }

            $rates = [];
            foreach ($xml->Currency as $currency) {
                $code = strtoupper((string) ($currency['Kod'] ?? ''));
                if ($code === '' || ! isset($wanted[$code])) {
                    continue;
                }
                $forexBuying = trim((string) ($currency->ForexBuying ?? ''));
                if ($forexBuying === '') {
                    continue;
                }
                $value = (float) str_replace(',', '.', $forexBuying);
                if ($value > 0) {
                    $rates[$code] = $value;
                }
            }

            return $rates;
        } catch (\Throwable $e) {
            Log::error('tcmb.exchange.exception', ['message' => $e->getMessage()]);

            return [];
        }
    }

    /**
     * USD/EUR için TCMB kurları.
     *
     * TCMB today.xml günde bir kez güncellenir. Eskiden 45 sn'lik cache süresi dolduğunda
     * ilk gelen (ve aynı anda gelen tüm) istekler TCMB'ye 12 sn'ye kadar bekleyen HTTP çağrısı
     * yapıyor, bu sırada DB bağlantısı açık kalıyordu. Artık Cache::flexible ile süre dolunca
     * eski değer hemen döner, yenileme yanıt gönderildikten sonra arka planda yapılır.
     * Başarısız çekimde son başarılı kur korunur.
     *
     * @return array<string, float>
     */
    public function getUsdEurCached(): array
    {
        $fresh = max(60, (int) config('services.tcmb.cache_ttl_seconds', 900));
        $stale = max($fresh, (int) config('services.tcmb.stale_ttl_seconds', 86400));

        return Cache::flexible(
            'tcmb_spot_rates_usd_eur',
            [$fresh, $stale],
            function (): array {
                $rates = $this->fetchFromTcmbTodayXml(['USD', 'EUR']);
                if ($rates === []) {
                    return (array) Cache::get('tcmb_spot_rates_usd_eur_last_good', []);
                }
                Cache::forever('tcmb_spot_rates_usd_eur_last_good', $rates);

                return $rates;
            }
        );
    }

    public function bustCache(): void
    {
        Cache::forget('tcmb_spot_rates_usd_eur');
    }

    /**
     * @param  Collection<int, \App\Models\Currency>  $currencies
     * @return Collection<int, \App\Models\Currency>
     */
    public function mergeUsdEurInto(Collection $currencies): Collection
    {
        $live = $this->getUsdEurCached();
        if ($live === []) {
            return $currencies;
        }

        return $currencies->map(function (\App\Models\Currency $currency) use ($live) {
            $code = strtoupper($currency->code);
            if (! isset($live[$code])) {
                return $currency;
            }
            $copy = clone $currency;
            $copy->exchange_rate = $live[$code];

            return $copy;
        });
    }
}
