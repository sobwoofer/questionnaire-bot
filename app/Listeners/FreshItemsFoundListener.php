<?php

namespace App\Listeners;

use App\Events\FreshItemsFound;
use Telegram\Bot\Api;

/**
 * Class FreshItemsFoundListener
 * @package App\Listeners
 * @property Api $telegram
 */
class FreshItemsFoundListener
{
    private $telegram;

    public function __construct(Api $telegram)
    {
        $this->telegram = $telegram;
    }

    /**
     * @param FreshItemsFound $event
     * @throws \Telegram\Bot\Exceptions\TelegramSDKException
     */
    public function handle(FreshItemsFound $event)
    {
        $message = 'Знайшов для тебе нових ' . count($event->links) . ' продуктів' . PHP_EOL;
        $message .= 'Фільтр: ' . $event->filter->title . PHP_EOL;
        foreach ($event->links as $link) {
            $message .= $link . PHP_EOL;
        }

        $this->telegram->sendMessage([
            'chat_id' => $event->filter->customer->chat_id,
            'text' => $message,
        ]);
    }
}
