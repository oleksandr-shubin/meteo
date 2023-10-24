<?php

namespace App\Domain\Subscription\Models;

use App\Domain\Shared\Models\City;
use Database\Factories\SubscriptionFactory;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class Subscription extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected static function newFactory(): SubscriptionFactory
    {
        return SubscriptionFactory::new();
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function getStatusAttribute(): string
    {
        if ($this->paused_till === null) {
            return 'active';
        }

        if (Carbon::now()->greaterThanOrEqualTo($this->paused_till)) {
            return 'active';
        }

        return 'Paused till: ' . $this->paused_till;
    }
}
