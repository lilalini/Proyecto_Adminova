<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeocodingService
{
    public function geocodeAddress(string $address): ?array
    {
        if (empty($address)) {
            return null;
        }

        try {
            $response = Http::withHeaders([
                'User-Agent' => 'Adminova/1.0',
                'Accept' => 'application/json',
            ])
            ->withOptions(['verify' => false]) // importante para evitar problemas de SSL
            ->timeout(10)
            ->get('https://nominatim.openstreetmap.org/search', [
                'q' => $address,
                'format' => 'json',
                'limit' => 1,
            ]);

            $data = $response->json();

            if ($response->successful() && !empty($data)) {
                return [
                    'lat' => $data[0]['lat'],
                    'lon' => $data[0]['lon'],
                ];
            }

            Log::warning('Geocoding returned no results', ['address' => $address]);

        } catch (\Throwable $e) {
            Log::error('Geocoding failed', [
                'address' => $address,
                'error' => $e->getMessage(),
            ]);
        }

        return null;
    }
}