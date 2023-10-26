<?php

namespace App\Domain\Subscription\Support;

use App\Domain\Shared\Models\AverageWeatherState;
use App\Domain\Shared\Models\City;
use App\Domain\Shared\Support\WeatherService;
use App\Domain\Visualcrossing\Support\VisualcrossingService;
use App\Domain\Weatherapi\Support\WeatherapiService;
use Illuminate\Support\Collection;

class AverageWeatherService
{
    private Collection $weatherProviders;

    public function __construct(
        private readonly VisualcrossingService $visualcrossingService,
        private readonly WeatherapiService $weatherapiService,
    )
    {
        $this->weatherProviders = collect();
        $this->weatherProviders->add($this->visualcrossingService);
        $this->weatherProviders->add($this->weatherapiService);
    }

    public function findAverage(City $city): AverageWeatherState
    {
        $averageWeather = new AverageWeatherState();
        $averageWeather->city_id = $city->id;
        $averageWeather->precipitation_mm = 0;
        $averageWeather->uv = 0;

        $averageWeather = $this->weatherProviders
            ->reduce(
                function (AverageWeatherState $averageWeather, WeatherService $provider) use ($city) {
                    $currentWeather = $provider->findCurrentState($city);
                    $averageWeather->precipitation_mm =
                        collect([$averageWeather->precipitation_mm, $currentWeather->precipitation_mm])->avg();
                    $averageWeather->uv = collect([$averageWeather->uv, $currentWeather->uv])->avg();
                    return $averageWeather;
                },
                $averageWeather
            );

        $averageWeather->save();
        return $averageWeather;
    }
}
