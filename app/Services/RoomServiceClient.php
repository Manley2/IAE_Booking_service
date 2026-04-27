<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;

class RoomServiceClient
{
    /**
     * @throws ConnectionException
     */
    public function findById(int $roomId): ?array
    {
        $response = Http::baseUrl(config('services.room_service.url'))
            ->timeout(config('services.room_service.timeout'))
            ->retry(
                config('services.room_service.retries'),
                config('services.room_service.retry_delay_ms')
            )
            ->get("/rooms/{$roomId}");

        if ($response->status() === 404) {
            return null;
        }

        if ($response->failed()) {
            throw new RequestException($response);
        }

        return $this->extractData($response->json());
    }

    /**
     * @throws ConnectionException
     */
    public function reserve(int $roomId): ?array
    {
        $response = Http::baseUrl(config('services.room_service.url'))
            ->timeout(config('services.room_service.timeout'))
            ->retry(
                config('services.room_service.retries'),
                config('services.room_service.retry_delay_ms')
            )
            ->post("/rooms/{$roomId}/reserve");

        if ($response->status() === 404) {
            return null;
        }

        if ($response->failed()) {
            throw new RequestException($response);
        }

        return $this->extractData($response->json());
    }

    /**
     * @throws ConnectionException
     */
    public function release(int $roomId): ?array
    {
        $response = Http::baseUrl(config('services.room_service.url'))
            ->timeout(config('services.room_service.timeout'))
            ->retry(
                config('services.room_service.retries'),
                config('services.room_service.retry_delay_ms')
            )
            ->post("/rooms/{$roomId}/release");

        if ($response->status() === 404) {
            return null;
        }

        if ($response->failed()) {
            throw new RequestException($response);
        }

        return $this->extractData($response->json());
    }

    private function extractData(mixed $payload): ?array
    {
        if (is_array($payload) && isset($payload['data']) && is_array($payload['data'])) {
            return $payload['data'];
        }

        return is_array($payload) ? $payload : null;
    }
}
