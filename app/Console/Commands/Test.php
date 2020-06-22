<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Telegram\Bot\Api;
use Telegram\Bot\Laravel\Facades\Telegram;

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

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
//        $telegram = new Api('652236963:AAH3cyoQASEhyuaeao-MAWjbKZCmsjK1Czk');
//        $me = $telegram->getMe();

        $response = Telegram::getMe();

        $botId = $response->getId();
        $firstName = $response->getFirstName();
        $username = $response->getUsername();
    }
}
