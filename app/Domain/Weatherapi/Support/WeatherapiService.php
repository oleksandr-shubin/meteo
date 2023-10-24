<?php

namespace App\Domain\Weatherapi\Support;

use App\Domain\Shared\Dto\WeatherStateDto;
use Illuminate\Support\Str;

class WeatherapiService
{
    public function __construct(
        private readonly WeatherapiClient $client
    ) {
    }

    public function findCurrentState(string $cityName): WeatherStateDto
    {
        $currentWeather = $this->client->getCurrentByCity($cityName);
        return new WeatherStateDto(
            $cityName,
            $currentWeather['current']['uv'],
            $currentWeather['current']['precip_mm']
        );
    }

    public function isValidCity(string $cityName): bool
    {
        $timeZone = $this->client->getTimeZoneByCity($cityName);
        $weatherApiCityName = $timeZone['location']['name'] ?? null;
        if ($weatherApiCityName === null) {
            return false;
        }

        return Str::lower($cityName) === Str::lower($weatherApiCityName);
    }
}
