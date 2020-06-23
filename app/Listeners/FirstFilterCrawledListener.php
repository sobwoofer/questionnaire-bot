<?php

namespace App\Listeners;

use App\Events\FirstFilterCrawled;
use Telegram\Bot\Api;

/**
 * Class FirstFilterCrawledListener
 * @package App\Listeners
 */
class FirstFilterCrawledListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * @param FirstFilterCrawled $event
     * @throws \Telegram\Bot\Exceptions\TelegramSDKException
     */
    public function handle(FirstFilterCrawled $event): void
    {
        $telegramApiClient = new Api('652236963:AAH3cyoQASEhyuaeao-MAWjbKZCmsjK1Czk');

        $message = 'I just checked your filter ' . $event->filter->spot_type
            . ' and found ' . $event->itemsCount . ' products. I notice you when there will be new items ' . PHP_EOL;

        $telegramApiClient->sendMessage([
            'chat_id' => $event->filter->customer->chat_id,
            'text' => $message,
        ]);
    }
}
