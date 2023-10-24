<?php

namespace App\Domain\Subscription\Actions;

use App\Domain\Shared\Models\City;
use App\Domain\Subscription\Models\Subscription;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\ValidationException;

class StoreSubscriptionAction
{
    public function execute(array $subscriptionData): void
    {
        $this->validate($subscriptionData);

        $city = City::firstOrCreate([
           'name' => $subscriptionData['city_name']
        ]);

        request()->user()->subscriptions()->create([
            'city_id' => $city->id,
            'precipitation_threshold_mm' => $subscriptionData['precipitation_threshold_mm'],
            'uv_threshold' => $subscriptionData['uv_threshold']
        ]);
    }

    private function validate(array $subscriptionData): void
    {
        $subscriptionExists = Subscription
            ::whereHas('city', fn (Builder $query) => $query->whereName($subscriptionData['city_name']))
            ->where([
                'precipitation_threshold_mm' => $subscriptionData['precipitation_threshold_mm'],
                'uv_threshold' => $subscriptionData['uv_threshold']
            ])
            ->exists();

        if ($subscriptionExists) {
            throw ValidationException::withMessages(['city_name' => 'Subscription with same parameters already exists']);
        }
    }
}
