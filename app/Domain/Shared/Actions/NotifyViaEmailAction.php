<?php

namespace App\Domain\Shared\Actions;

use App\Domain\Shared\Dto\WeatherStateDto;
use App\Domain\Shared\Notifications\SevereWeatherDetected;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Notification;

class NotifyViaEmailAction
{
    private const EMAIL_ADDRESS = 'test@mail.com';

    public function execute(Collection $triggeredParameters): void
    {
        Notification::route('mail', self::EMAIL_ADDRESS)
            ->notify(new SevereWeatherDetected($triggeredParameters));
    }
}
