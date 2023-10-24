<?php

namespace App\Domain\Shared\Models;

use App\Domain\Subscription\Models\Subscription;
use Database\Factories\CityFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class City extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected static function newFactory(): CityFactory
    {
        return CityFactory::new();
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }
}
