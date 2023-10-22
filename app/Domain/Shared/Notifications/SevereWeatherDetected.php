<?php

namespace App\Domain\Shared\Notifications;

use App\Domain\Shared\Dto\WeatherParameterDto;
use App\Domain\Shared\Dto\WeatherStateDto;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use NotificationChannels\Telegram\TelegramMessage;

class SevereWeatherDetected extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        private Collection $triggeredParameters
    ) {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'telegram'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $message = new MailMessage();
        $message->line('Hello there!');

        $this->triggeredParameters->reduce(
            fn ($message, WeatherParameterDto $parameter) => $message->line(
                Str::of($parameter->name)
                    ->append(': ')->append($parameter->value)->append(' ')->append($parameter->unit)
                    ->append(' (>=')->append($parameter->threshold)->append(')')
            ),
            $message
        );

        $message->line('Thank you for using our application!');
        return $message;
    }

    public function toTelegram(object $notifiable)
    {
        $message = TelegramMessage::create();
        $message->content("Hello there!\n");

        $this->triggeredParameters->reduce(
            fn ($message, WeatherParameterDto $parameter) => $message->line(
                Str::of($parameter->name)
                    ->append(': ')->append($parameter->value)->append(' ')->append($parameter->unit)
                    ->append(' (>= ')->append($parameter->threshold)->append(')')
            ),
            $message
        );

        $message->line('Thank you for using our application!');
        return $message;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return $this->triggeredParameters->toArray();
    }
}
