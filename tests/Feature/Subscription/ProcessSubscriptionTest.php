<?php

namespace Tests\Feature\Subscription;

use App\Domain\Auth\Models\User;
use App\Domain\Subscription\Models\Subscription;
use App\Domain\Subscription\Notifications\SevereWeatherNotification;
use App\Domain\Visualcrossing\Support\VisualcrossingClient;
use App\Domain\Weatherapi\Actions\PollCurrentWeatherAction;
use App\Domain\Weatherapi\Support\WeatherapiClient;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

/**
 * @group subscription
 */
class ProcessSubscriptionTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->instance(
            WeatherapiClient::class,
            Mockery::mock(WeatherapiClient::class, function (MockInterface $mock) {
                $mock
                    ->shouldReceive('getCurrentByCity')
                    ->andReturn([
                        'current' => [
                            'precip_mm' => $this->faker->numberBetween(100, 200),
                            'uv' => $this->faker->numberBetween(5, 8),
                        ]
                    ]);
            })
        );

        $this->instance(
            VisualcrossingClient::class,
            Mockery::mock(VisualcrossingClient::class, function (MockInterface $mock) {
                $mock
                    ->shouldReceive('getTimelineByCity')
                    ->andReturn([
                        'currentConditions' => [
                            'precip' => $this->faker->numberBetween(100, 200),
                            'uvindex' => $this->faker->numberBetween(5, 8),
                        ]
                    ]);
            })
        );
    }

    /**
     * @test
     * @group failing
     */
    public function it_can_process_triggered_subscriptions(): void
    {
        Notification::fake();

        $tirggeredSubscriptionsCount = 3;

        $user = User::factory()
            ->has(Subscription::factory([
                'precipitation_threshold_mm' => $this->faker->numberBetween(1, 9),
                'uv_threshold' => $this->faker->numberBetween(1, 4),
            ])->count($tirggeredSubscriptionsCount))
            ->create();

        $this->artisan('subscription:process')->assertSuccessful();

        Notification::assertSentTo(
            $user,
            SevereWeatherNotification::class,
            function (SevereWeatherNotification $notification, array $channels) {
                return collect($channels)->contains('mail')
                    && collect($channels)->contains('telegram');
            }
        );
        Notification::assertSentTimes(SevereWeatherNotification::class, $tirggeredSubscriptionsCount);
    }

    /**
     * @test
     */
    public function it_can_process_only_triggered_subscriptions(): void
    {
        Notification::fake();

        $tirggeredSubscriptionsCount = 2;

        $user = User::factory()
            ->has(Subscription::factory([
                'precipitation_threshold_mm' => $this->faker->numberBetween(1, 9),
                'uv_threshold' => $this->faker->numberBetween(1, 4),
            ])->count($tirggeredSubscriptionsCount))
            ->has(Subscription::factory([
                'precipitation_threshold_mm' => $this->faker->numberBetween(201, 500),
                'uv_threshold' => $this->faker->numberBetween(9, 11),
            ])->count(1))
            ->create();

        $this->artisan('subscription:process')->assertSuccessful();

        Notification::assertSentTo($user, SevereWeatherNotification::class);
        Notification::assertSentTimes(SevereWeatherNotification::class, $tirggeredSubscriptionsCount);
    }

    /**
     * @test
     */
    public function it_can_adapt_message_depending_on_threshold_crossed(): void
    {
        Notification::fake();

        $user = User::factory()
            ->has(Subscription::factory([
                'precipitation_threshold_mm' => $this->faker->numberBetween(1, 9),
                'uv_threshold' => $this->faker->numberBetween(5, 11),
            ]))
            ->create();

        $this->artisan('subscription:process')->assertSuccessful();

        Notification::assertSentTo(
            $user,
            SevereWeatherNotification::class,
            function (SevereWeatherNotification $notification) {
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
    }
}
