<?php

namespace App\Domain\Visualcrossing\Support;

use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;

class VisualcrossingClient
{
    public const BASE_URL = 'https://weather.visualcrossing.com/VisualCrossingWebServices/rest/services';

    public function getTimelineByCity(string $city): array
    {
        $endpoint = '/timeline/' . $city;
        $response = Http::get(self::BASE_URL . $endpoint, [
            'key' => config('meteo.visualcrossing.api_key'),
            'unitGroup' => 'metric',
            'contentType' => 'json',
        ]);

        if ($response->status() !== Response::HTTP_OK) {
            throw new \Exception(sprintf('Visualcrossing response %s', $response->status()));
        }

        return $response->json();
    }
}
