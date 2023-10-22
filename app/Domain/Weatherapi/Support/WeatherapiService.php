<?php

namespace App\Domain\Weatherapi\Support;

use App\Domain\Shared\Dto\WeatherStateDto;

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
}
