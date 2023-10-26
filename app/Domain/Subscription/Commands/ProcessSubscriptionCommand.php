<?php

namespace App\Domain\Subscription\Commands;

use App\Domain\Subscription\Actions\ProcessSubscriptionAction;
use Illuminate\Console\Command;

class ProcessSubscriptionCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscription:process';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notify subscribers';

    public function __construct(
        private readonly ProcessSubscriptionAction $processSubscriptionAction
        ,
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
        $this->processSubscriptionAction->execute();
    }
}
