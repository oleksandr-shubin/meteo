<?php

namespace App\Domain\Visualcrossing\Support;

use App\Domain\Shared\Enums\WeatherProvider;
use App\Domain\Shared\Models\City;
use App\Domain\Shared\Support\WeatherService;
use App\Domain\Shared\Models\WeatherState;

class VisualcrossingService implements WeatherService
{
    public function __construct(
        private readonly VisualcrossingClient $client
    ) {
    }

    public function findCurrentState(City $city): WeatherState
    {
        $currentWeather = $this->client->getTimelineByCity($city->name);
        return $city->weatherStates()->create([
            'provider_name' => WeatherProvider::VISUALCROSSING,
            'precipitation_mm' => $currentWeather['currentConditions']['precip'],
            'uv' => $currentWeather['currentConditions']['uvindex']
        ]);
    }
}
