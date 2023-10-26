<?php

namespace App\Domain\Shared\Support;

use App\Domain\Shared\Models\City;
use App\Domain\Shared\Models\WeatherState;

interface WeatherService
{
    public function findCurrentState(City $city): WeatherState;
}
