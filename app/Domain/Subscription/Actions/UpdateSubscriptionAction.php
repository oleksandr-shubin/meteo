<?php

namespace App\Domain\Subscription\Actions;

use App\Domain\Subscription\Models\Subscription;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;

class UpdateSubscriptionAction
{
    public function execute(Subscription $subscription, array $subscriptionData): void
    {
        $this->validate($subscription, $subscriptionData);

        $updateData = [
            'precipitation_threshold_mm' => $subscriptionData['precipitation_threshold_mm'],
            'uv_threshold' => $subscriptionData['uv_threshold'],
        ];

        if (isset($subscriptionData['pause_for'])) {
            $updateData['paused_till'] = Carbon::now()->addHours($subscriptionData['pause_for']);
        }

        $subscription->update($updateData);
    }

    private function validate(Subscription $subscription, array $subscriptionData): void
    {
        $subscriptionExists = Subscription
            ::whereHas('city', fn (Builder $query) => $query->whereName($subscription->city->name))
            ->where([
                'precipitation_threshold_mm' => $subscriptionData['precipitation_threshold_mm'],
                'uv_threshold' => $subscriptionData['uv_threshold']
            ])
            ->where('id', '<>', $subscription->id)
            ->exists();

        if ($subscriptionExists) {
            throw ValidationException::withMessages(['city_name' => 'Subscription with same parameters already exists']);
        }
    }
}
