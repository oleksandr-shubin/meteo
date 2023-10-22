<?php

namespace App\Domain\Shared\Actions;

use App\Domain\Shared\Dto\WeatherStateDto;
use App\Domain\Shared\Notifications\SevereWeatherDetected;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Notification;

class NotifyViaTelegramAction
{
    private const CHAT_ID = 335629780;

    public function execute(Collection $triggeredParameters): void
    {
        Notification::route('telegram', self::CHAT_ID)
            ->notify(new SevereWeatherDetected($triggeredParameters));
    }
}
