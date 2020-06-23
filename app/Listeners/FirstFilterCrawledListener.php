<?php

namespace App\Listeners;

use App\Events\FirstFilterCrawled;
use Telegram\Bot\Api;

/**
 * Class FirstFilterCrawledListener
 * @package App\Listeners
 * @property Api $telegram
 */
class FirstFilterCrawledListener
{
    private $telegram;

    public function __construct(Api $telegram)
    {
        $this->telegram = $telegram;
    }

    /**
     * @param FirstFilterCrawled $event
     */
    public function handle(FirstFilterCrawled $event): void
    {
        $message = 'I just checked your filter ' . $event->filter->spot_type
            . ' and found ' . $event->itemsCount . ' products. I notice you when there will be new items ' . PHP_EOL;

        $this->telegram->sendMessage([
            'chat_id' => $event->filter->customer->chat_id,
            'text' => $message,
        ]);
    }
}
