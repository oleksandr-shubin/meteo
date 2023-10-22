<?php

namespace Tests\Feature\Weatherapi;

use App\Domain\Shared\Notifications\SevereWeatherDetected;
use App\Domain\Weatherapi\Actions\PollCurrentWeatherAction;
use App\Domain\Weatherapi\Support\WeatherapiClient;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Tests\TestCase;

class PollForecastTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_poll_current_weather_and_notify_if_thresholds_crossed(): void
    {
        $pollCurrentWeatherAction = app()->make(PollCurrentWeatherAction::class);

        Notification::fake();
        Http::fake([
            WeatherapiClient::BASE_URL . '/current.json*' => Http::response([
                'current' => [
                    'uv' => 10,
                    'precip_mm' => 100,
                ]
            ])
        ]);

        $pollCurrentWeatherAction->execute();
        Notification::assertSentOnDemand(SevereWeatherDetected::class);
        Notification::assertSentTimes(SevereWeatherDetected::class, 2);
    }

    /**
     * @test
     */
    public function it_can_poll_current_weather_and_skip_if_thresholds_not_reached(): void
    {
        $pollCurrentWeatherAction = app()->make(PollCurrentWeatherAction::class);

        Notification::fake();
        Http::fake([
            WeatherapiClient::BASE_URL . '/current.json*' => Http::response([
                'current' => [
                    'uv' => 0.1,
                    'precip_mm' => 10,
                ]
            ])
        ]);

        $pollCurrentWeatherAction->execute();
        Notification::assertNothingSent();
    }

    /**
     * @test
     */
    public function it_can_adapt_message_depending_on_threshold_crossed(): void
    {
        $pollCurrentWeatherAction = app()->make(PollCurrentWeatherAction::class);

        Notification::fake();
        Http::fake([
            WeatherapiClient::BASE_URL . '/current.json*' => Http::response([
                'current' => [
                    'uv' => 0.1,
                    'precip_mm' => 100,
                ]
            ])
        ]);

        $pollCurrentWeatherAction->execute();
        Notification::assertSentOnDemand(
            SevereWeatherDetected::class,
            function (SevereWeatherDetected $notification) {
                foreach ($notification->via(new \StdClass()) as $channel) {
                    $method = Str::of($channel)->ucfirst()->prepend('to')->__toString();
                    $content = $notification->$method(new \StdClass());
                    return collect($content->introLines)
                            ->contains(fn ($line) => Str::of($line)->contains('Precipitations'))
                        && collect($content->introLines)
                            ->doesntContain(fn ($line) => Str::of($line)->contains('UV index'));
                }
            }
        );
        Notification::assertSentTimes(SevereWeatherDetected::class, 2);
    }
}
