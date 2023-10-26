<?php

namespace App\Domain\Shared\Enums;

enum WeatherProvider: string
{
    case WEATHERAPI = 'WEATHERAPI';
    case VISUALCROSSING = 'VISUALCROSSING';
}
