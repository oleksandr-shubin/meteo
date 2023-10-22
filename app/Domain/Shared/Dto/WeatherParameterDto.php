<?php

namespace App\Domain\Shared\Dto;

use Spatie\LaravelData\Data;

class WeatherParameterDto extends Data
{
    public function __construct(
        public string $name,
        public float $value,
        public ?string $unit,
        public ?float $threshold,
    ) {
    }
}
