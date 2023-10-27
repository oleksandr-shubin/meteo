<?php

namespace App\Domain\Subscription\Policies;

use App\Domain\Auth\Models\User;
use App\Domain\Subscription\Models\Subscription;

class SubscriptionPolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Subscription $subscription): bool
    {
        return $user->owns($subscription);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Subscription $subscription): bool
    {
        return $user->owns($subscription);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Subscription $subscription): bool
    {
        return $user->owns($subscription);
    }
}
