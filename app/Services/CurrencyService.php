<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CurrencyService
{
    private string $apiKey;
    private string $baseUrl = 'https://api.currencyapi.com/v3';
    private int $cacheMinutes = 60;

    public function __construct()
    {
        $this->apiKey = env('CURRENCY_API_KEY');
    }

    /**
     * Get HUF to EUR exchange rate from external API with caching
     */
    public function getHufToEurRate(): ?float
    {
        return Cache::remember('huf_to_eur_rate', $this->cacheMinutes * 60, function () {
            try {
                $response = Http::get("{$this->baseUrl}/latest", [
                    'apikey' => $this->apiKey,
                    'currencies' => 'EUR',
                    'base_currency' => 'HUF'
                ]);

                if ($response->successful()) {
                    $data = $response->json();

                    if (isset($data['data']['EUR']['value'])) {
                        $rate = (float) $data['data']['EUR']['value'];
                        Log::info('Currency rate fetched', ['HUF_to_EUR' => $rate]);
                        return $rate;
                    }
                }

                Log::error('Currency API error', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);

                return null;

            } catch (\Exception $e) {
                Log::error('Currency API exception', [
                    'message' => $e->getMessage()
                ]);
                return null;
            }
        });
    }

    /**
     * Convert HUF amount to EUR
     */
    public function convertHufToEur(float|string $hufAmount): ?float
    {
        $rate = $this->getHufToEurRate();

        if ($rate === null) {
            return null;
        }

        return round((float)$hufAmount * $rate, 2);
    }

    /**
     * Format price in both currencies
     */
    public function formatPrice(float|string $hufPrice): array
    {
        $eurPrice = $this->convertHufToEur($hufPrice);

        return [
            'huf' => number_format((float)$hufPrice, 2, '.', ''),
            'eur' => $eurPrice ? number_format($eurPrice, 2, '.', '') : null
        ];
    }

    /**
     * Clear cache (if you want to refresh the exchange rate)
     */
    public function clearCache(): void
    {
        Cache::forget('huf_to_eur_rate');
    }

    /**
     * Get rate info including cache timestamp
     */
    public function getRateInfo(): array
    {
        $rate = $this->getHufToEurRate();
        $cachedAt = Cache::get('huf_to_eur_rate_timestamp');

        return [
            'rate' => $rate,
            'base_currency' => 'HUF',
            'target_currency' => 'EUR',
            'cached_at' => $cachedAt,
            'cache_expires_in_minutes' => $this->cacheMinutes
        ];
    }
}
