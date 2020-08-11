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
        $message = 'Я тільки що перевірив твій ' . $event->filter->spot_type . ' ' . $event->filter->title
            . ' та знайшов там ' . $event->itemsCount . ' товарів. Я повідолю тебе коли там з"явиться щось нове ' . PHP_EOL;

        $this->telegram->sendMessage([
            'chat_id' => $event->filter->customer->chat_id,
            'text' => $message,
        ]);
    }
}
