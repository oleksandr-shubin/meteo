<?php

namespace App\Domain\Shared\Dto;

use Spatie\LaravelData\Data;

class WeatherStateDto extends Data
{
    public function __construct(
        public string $cityName,
        public float $uvIndex,
        public float $precipitationMm,
    ) {
    }
}
