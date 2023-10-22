<?php

namespace App\Domain\Weatherapi\Commands;

use App\Domain\Weatherapi\Actions\PollCurrentWeatherAction;
use Illuminate\Console\Command;

class PollWeather extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'weatherapi:poll-weather';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Weatherapi: poll current weather';

    public function __construct(
        private readonly PollCurrentWeatherAction $pollCurrentWeatherAction,
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->pollCurrentWeatherAction->execute();
    }
}
