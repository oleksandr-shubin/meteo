<?php

namespace App\Domain\Weatherapi\Actions;

use App\Domain\Shared\Actions\NotifyViaEmailAction;
use App\Domain\Shared\Actions\NotifyViaTelegramAction;
use App\Domain\Shared\Dto\WeatherParameterDto;
use App\Domain\Shared\Dto\WeatherStateDto;
use App\Domain\Weatherapi\Support\WeatherapiService;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class PollCurrentWeatherAction
{
    public const CITY_NAME = 'London';
    public const PRECIPITATION_MM_THRESHOLD = 30;
    public const UV_INDEX_THRESHOLD = 3;

    public function __construct(
        private readonly WeatherapiService $openWeatherService,
        private readonly NotifyViaEmailAction $notifyViaEmailAction,
        private readonly NotifyViaTelegramAction $notifyViaTelegramAction
    ) {
    }

    public function execute(): void
    {
        $currentWeather = $this->openWeatherService->findCurrentState(self::CITY_NAME);
        $triggeredParameters = $this->findTriggeredParameters($currentWeather);
        if ($triggeredParameters->isEmpty()) {
            return;
        }

        $this->notifyViaEmailAction->execute($triggeredParameters);
        $this->notifyViaTelegramAction->execute($triggeredParameters);
    }

    private function findTriggeredParameters(WeatherStateDto $currentWeather): Collection
    {
        $crossedParameters = collect();

        if ($currentWeather->precipitationMm >= self::PRECIPITATION_MM_THRESHOLD) {
            $crossedParameters->push(new WeatherParameterDto(
                name: 'Precipitations',
                value: $currentWeather->precipitationMm,
                unit: 'mm',
                threshold: self::PRECIPITATION_MM_THRESHOLD,
            ));
        }

        if ($currentWeather->uvIndex >= self::UV_INDEX_THRESHOLD) {
            $crossedParameters->push(new WeatherParameterDto(
                name: 'UV index',
                value: $currentWeather->uvIndex,
                unit: null,
                threshold: self::UV_INDEX_THRESHOLD,
            ));
        }

        return $crossedParameters;
    }
}
