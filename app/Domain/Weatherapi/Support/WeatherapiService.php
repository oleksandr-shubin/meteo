<?php

namespace App\Domain\Weatherapi\Support;

use App\Domain\Shared\Enums\WeatherProvider;
use App\Domain\Shared\Models\City;
use App\Domain\Shared\Models\WeatherState;
use App\Domain\Shared\Support\WeatherService;
use Exception;
use Illuminate\Support\Str;

class WeatherapiService implements WeatherService
{
    public function __construct(
        private readonly WeatherapiClient $client
    ) {
    }

    public function findCurrentState(City $city): WeatherState
    {
        $currentWeather = $this->client->getCurrentByCity($city->name);
        return $city->weatherStates()->create([
            'provider_name' => WeatherProvider::WEATHERAPI,
            'precipitation_mm' => $currentWeather['current']['precip_mm'],
            'uv' => $currentWeather['current']['uv']
        ]);
    }

    public function isValidCity(string $cityName): bool
    {
        try {
            $timeZone = $this->client->getTimeZoneByCity($cityName);
            $weatherApiCityName = $timeZone['location']['name'] ?? null;
            if ($weatherApiCityName === null) {
                return false;
            }

            return Str::lower($cityName) === Str::lower($weatherApiCityName);
        } catch (Exception $e) {
            report($e);
            return false;
        }
    }
}
