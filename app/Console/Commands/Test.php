<?php

namespace App\Console\Commands;

use App\Eloquent\Customer;
use App\Events\States\AddFilter;
use App\Events\States\Info;
use App\Events\States\RemoveFilter;
use App\Events\States\ShowFilters;
use App\Events\States\Start;
use App\Listeners\States\AddFilterListener;
use App\Listeners\States\InfoListener;
use App\Listeners\States\RemoveFilterListener;
use App\Listeners\States\ShowFiltersListener;
use App\Listeners\States\StartListener;
use App\Services\FlowService;
use Illuminate\Console\Command;
use App\Events\States\Hunting;
use Telegram\Bot\Api;

/**
 * Class Test
 * @package App\Console\Commands
 * @property FlowService $flowService
 * @property Api $telegram
 */
class Test extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    private $telegram;
    private $flowService;


    public function __construct(Api $telegram, FlowService $flowService)
    {
        $this->telegram = $telegram;
        $this->flowService = $flowService;
        parent::__construct();
    }

    /**
     * @return string|void
     * @throws \Telegram\Bot\Exceptions\TelegramSDKException
     */
    public function handle()
    {
        $this->telegram->removeWebhook();

        $this->runBot();
//        while (true) {
//            $this->runBot();
//            sleep(2);
//        }

    }

    /**
     *
     */
    public function runBot(): void
    {
        $updates = $this->telegram->getUpdates();

        foreach ($updates as $update) {
            $this->flowService->processUpdate($update);
        }
    }

}
