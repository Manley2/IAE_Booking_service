<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;

class CustomerServiceClient
{
    /**
     * @throws ConnectionException
     */
    public function findById(int $customerId): ?array
    {
        $response = Http::baseUrl(config('services.customer_service.url'))
            ->timeout(config('services.customer_service.timeout'))
            ->retry(
                config('services.customer_service.retries'),
                config('services.customer_service.retry_delay_ms')
            )
            ->get("/customers/{$customerId}");

        if ($response->status() === 404) {
            return null;
        }

        if ($response->failed()) {
            throw new RequestException($response);
        }

        $payload = $response->json();

        if (is_array($payload) && isset($payload['data']) && is_array($payload['data'])) {
            return $payload['data'];
        }

        return is_array($payload) ? $payload : null;
    }
}
