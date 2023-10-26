<?php

namespace Database\Factories;

use App\Domain\Auth\Models\User;
use App\Domain\Shared\Models\City;
use App\Domain\Subscription\Models\Subscription;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Subscription>
 */
class SubscriptionFactory extends Factory
{
    protected $model = Subscription::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'city_id' => City::factory(),
            'precipitation_threshold_mm' => $this->faker->numberBetween(1, 500),
            'uv_threshold' => $this->faker->numberBetween(1, 11),
        ];
    }
}
