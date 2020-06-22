<?php

namespace App\Listeners;

use App\Events\FreshItemsFound;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Telegram\Bot\Api;

class FreshItemsFoundListener
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
     * @param FreshItemsFound $event
     * @return string
     * @throws \Telegram\Bot\Exceptions\TelegramSDKException
     */
    public function handle(FreshItemsFound $event)
    {
        $telegramApiClient = new Api('652236963:AAH3cyoQASEhyuaeao-MAWjbKZCmsjK1Czk');

        $message = 'I found new ' . count($event->links) . ' products for you' . PHP_EOL;

        foreach ($event->links as $link) {
            $message .= $link . PHP_EOL;
        }
        $telegramApiClient->sendMessage([
            'chat_id' => $event->filter->customer->chat_id,
            'text' => $message,
        ]);
    }
}
