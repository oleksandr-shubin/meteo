<?php

namespace Tests\Feature\Subscription;

use App\Domain\Auth\Models\User;
use App\Domain\Subscription\Models\Subscription;
use Tests\TestCase;

/**
 * @group subscription
 */
class SubscriptionPolicyTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_edit_only_own_subscription(): void
    {
        $user = User::factory()->create();
        $subscription = Subscription::factory()->create(['user_id' => $user->id]);

        $evilUser = User::factory()->create();
        $response = $this->actingAs($evilUser)
            ->get(route('subscription.edit', ['subscription' => $subscription->id]));

        $response->assertForbidden();
    }

    /**
     * @test
     */
    public function it_can_update_only_own_subscription(): void
    {
        $user = User::factory()->create();
        $subscription = Subscription::factory()->create(['user_id' => $user->id]);

        $evilUser = User::factory()->create();
        $formData = [
            'precipitation_threshold_mm' => $this->faker->numberBetween(0, 500),
            'uv_threshold' => $this->faker->numberBetween(0, 11),
        ];

        $response = $this->actingAs($evilUser)
            ->patch(route('subscription.update', ['subscription' => $subscription->id]), $formData);
        $response->assertForbidden();

        $this->assertDatabaseMissing('subscriptions', [
            'id' => $subscription->id,
            'precipitation_threshold_mm' => $formData['precipitation_threshold_mm'],
            'uv_threshold' => $formData['uv_threshold']
        ]);
    }

    /**
     * @test
     */
    public function it_can_destroy_only_own_subscription(): void
    {
        $user = User::factory()->create();
        $subscription = Subscription::factory()->create(['user_id' => $user->id]);

        $evilUser = User::factory()->create();
        $response = $this->actingAs($evilUser)
            ->delete(route('subscription.destroy', ['subscription' => $subscription->id]));
        $response->assertForbidden();

        $this->assertDatabaseHas('subscriptions', [
            'id' => $subscription->id,
        ]);
    }
}
