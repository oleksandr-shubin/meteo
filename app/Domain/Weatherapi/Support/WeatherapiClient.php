<?php

namespace App\Domain\Weatherapi\Support;

use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;

class WeatherapiClient
{
    public const BASE_URL = 'http://api.weatherapi.com/v1';

    public function getCurrentByCity(string $city): array
    {
        $endpoint = '/current.json';
        $response = Http::get(self::BASE_URL . $endpoint, [
            'key' => config('meteo.weatherapi.api_key'),
            'q' => $city,
            'aqi' => 'no',
        ]);

        if ($response->status() !== Response::HTTP_OK) {
            throw new \Exception(sprintf('Weatherapi response %s', $response->status()));
        }

        return $response->json();
    }

    public function getTimeZoneByCity(string $city): array
    {
        $endpoint = '/timezone.json';
        $response = Http::get(self::BASE_URL . $endpoint, [
            'key' => config('meteo.weatherapi.api_key'),
            'q' => $city,
        ]);

        if ($response->status() !== Response::HTTP_OK) {
            throw new \Exception(sprintf('Weatherapi response %s', $response->status()));
        }

        return $response->json();
    }
}
