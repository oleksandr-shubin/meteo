<?php

namespace App\Domain\Subscription\Actions;

use App\Domain\Shared\Dto\WeatherParameterDto;
use App\Domain\Shared\Models\AverageWeatherState;
use App\Domain\Shared\Models\City;
use App\Domain\Subscription\Models\Subscription;
use App\Domain\Subscription\Notifications\SevereWeatherNotification;
use App\Domain\Subscription\Support\AverageWeatherService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class ProcessSubscriptionAction
{
    public function __construct(
        private readonly AverageWeatherService $averageWeatherService
    ) {
    }

    public function execute(): void
    {
        City::whereHas('subscriptions', fn (Builder $query) => $query->active())
            ->chunk(100, function (Collection $cities) {
                $cities->each(function(City $city) {
                    $averageWeather = $this->averageWeatherService->findAverage($city);
                    $city->subscriptions()->triggered($averageWeather)->with('user')
                        ->each(function (Subscription $subscription) use($averageWeather) {
                            $triggeredParameters = $this->findTriggeredParameters($averageWeather, $subscription);
                            $notification = new SevereWeatherNotification($triggeredParameters);
                            $subscription->user->notify($notification);
                        });
                });
            });


    }

    private function findTriggeredParameters(
        AverageWeatherState $currentWeather,
        Subscription $subscription
    ): \Illuminate\Support\Collection
    {
        $crossedParameters = collect();

        if ($currentWeather->precipitation_mm >= $subscription->precipitation_threshold_mm) {
            $crossedParameters->push(new WeatherParameterDto(
                name: 'Precipitations',
                value: $currentWeather->precipitation_mm,
                unit: 'mm',
                threshold: $subscription->precipitation_threshold_mm,
            ));
        }

        if ($currentWeather->uv >= $subscription->uv_threshold) {
            $crossedParameters->push(new WeatherParameterDto(
                name: 'UV index',
                value: $currentWeather->uv,
                unit: null,
                threshold: $subscription->uv_threshold,
            ));
        }

        return $crossedParameters;
    }
}
