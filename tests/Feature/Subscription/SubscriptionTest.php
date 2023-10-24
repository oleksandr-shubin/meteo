<?php

namespace Tests\Feature\Subscription;

use App\Domain\Auth\Models\User;
use App\Domain\Subscription\Models\Subscription;
use App\Domain\Weatherapi\Support\WeatherapiService;
use Illuminate\Support\Carbon;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

/**
 * @group subscription
 */
class SubscriptionTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->instance(
            WeatherapiService::class,
            Mockery::mock(WeatherapiService::class, function (MockInterface $mock) {
                $mock
                    ->shouldReceive('isValidCity')
                    ->andReturn(true);
            })
        );
    }

    /**
     * @test
     */
    public function it_can_index_subscription(): void
    {
        $user = User::factory()
            ->hasSubscriptions(10)
            ->create();

        $response = $this->actingAs($user)
            ->get(route('subscription.index'));

        $response->assertOk();
        $response->assertSee(route('subscription.create'));

        foreach ($user->subscriptions as $subscription) {
            $response->assertSee($subscription->city->name);
            $response->assertSee($subscription->precipitation_threshold_mm);
            $response->assertSee($subscription->uv_threshold);

            $response->assertSee(route('subscription.edit', ['subscription' => $subscription->id]));
            $response->assertSee(route('subscription.destroy', ['subscription' => $subscription->id]));
        }
    }

    /**
     * @test
     */
    public function it_can_create_subscription(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->get(route('subscription.create'));

        $response->assertOk();
        $response->assertSee(route('subscription.store'));
        $response->assertSee(route('subscription.index'));
    }

    /**
     * @test
     */
    public function it_can_store_subscription(): void
    {
        $user = User::factory()->create();

        $formData = [
            'city_name' => $this->faker->city(),
            'precipitation_threshold_mm' => $this->faker->numberBetween(0, 500),
            'uv_threshold' => $this->faker->numberBetween(0, 11),
        ];

        $response = $this->actingAs($user)->post(route('subscription.store'), $formData);
        $response->assertRedirect();

        $this->assertDatabaseHas('subscriptions', [
            'precipitation_threshold_mm' => $formData['precipitation_threshold_mm'],
            'uv_threshold' => $formData['uv_threshold']
        ]);
        $this->assertDatabaseHas('cities', [
           'name' => $formData['city_name']
        ]);
    }

    /**
     * @test
     */
    public function it_can_edit_subscription(): void
    {
        $user = User::factory()->create();
        $subscription = Subscription::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)
            ->get(route('subscription.edit', ['subscription' => $subscription->id]));

        $response->assertOk();
        $response->assertSee(route('subscription.update', ['subscription' => $subscription->id]));
        $response->assertSee(route('subscription.index'));
    }

    /**
     * @test
     */
    public function it_can_update_subscription(): void
    {
        $user = User::factory()->create();
        $subscription = Subscription::factory()->create(['user_id' => $user->id]);

        $formData = [
            'precipitation_threshold_mm' => $this->faker->numberBetween(0, 500),
            'uv_threshold' => $this->faker->numberBetween(0, 11),
        ];

        $response = $this->actingAs($user)
            ->patch(route('subscription.update', ['subscription' => $subscription->id]), $formData);
        $response->assertRedirect();

        $this->assertDatabaseHas('subscriptions', [
            'id' => $subscription->id,
            'precipitation_threshold_mm' => $formData['precipitation_threshold_mm'],
            'uv_threshold' => $formData['uv_threshold']
        ]);
    }

    /**
     * @test
     */
    public function it_can_pause_subscription(): void
    {
        $user = User::factory()->create();
        $subscription = Subscription::factory()->create(['user_id' => $user->id]);

        $pauseForHours = 10;

        $formData = [
            'precipitation_threshold_mm' => $subscription->precipitation_threshold_mm,
            'uv_threshold' => $subscription->uv_threshold,
            'pause_for' => $pauseForHours,
        ];

        $response = $this->actingAs($user)
            ->patch(route('subscription.update', ['subscription' => $subscription->id]), $formData);
        $response->assertRedirect();

        $this->assertDatabaseHas('subscriptions', [
            'id' => $subscription->id,
            'paused_till' => Carbon::now()->addHours($pauseForHours),
        ]);
    }

    /**
     * @test
     */
    public function it_can_destroy_subscription(): void
    {
        $user = User::factory()->create();
        $subscription = Subscription::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)
            ->delete(route('subscription.destroy', ['subscription' => $subscription->id]));
        $response->assertRedirect();

        $this->assertDatabaseMissing('subscriptions', [
            'id' => $subscription->id,
        ]);
    }
}
