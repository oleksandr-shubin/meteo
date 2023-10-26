<?php

namespace App\Domain\Subscription\Models;

use App\Domain\Auth\Models\User;
use App\Domain\Shared\Models\AverageWeatherState;
use App\Domain\Shared\Models\City;
use App\Domain\Shared\Models\WeatherState;
use Database\Factories\SubscriptionFactory;
use Illuminate\Database\Eloquent\Builder;
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeActive(Builder $query): void
    {
        $query
            ->whereNull('paused_till')
            ->orWhere('paused_till', '<', Carbon::now());
    }

    public function scopeTriggered(Builder $query, AverageWeatherState $weather): void
    {
        $query
            ->where('precipitation_threshold_mm', '<', $weather->precipitation_mm)
            ->orWhere('uv_threshold', '<', $weather->uv);
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
