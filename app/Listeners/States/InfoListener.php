<?php

namespace App\Listeners\States;

use App\Eloquent\Customer;
use App\Eloquent\CustomerFilter;
use App\Events\States\Info;
use App\Events\States\ShowFilters;
use Telegram\Bot\Api;
use Telegram\Bot\Objects\Chat;

/**
 * Class InfoListener
 * @package App\Listeners\States
 */
class InfoListener
{
    public const ACTION = 'Справка';

    /**
     * @param Info $event
     * @throws \Telegram\Bot\Exceptions\TelegramSDKException
     */
    public function handle(Info $event): void
    {
        $message = $event->update->getMessage();
        $event->telegramApiClient->sendChatAction(['chat_id' => $message->getChat()->getId(), 'action' => 'typing']);

        if ($message->getText() === self::ACTION) {
            $this->sendInfo($event->telegramApiClient, $message->getChat());
            $event->customer->setState(Customer::STATE_HUNTING);
        }

        $event->customer->setUpdateId($event->update->getUpdateId());
    }

    /**
     * @param Api $client
     * @param Chat $chat
     */
    private function sendInfo(Api $client, Chat $chat): void
    {
        $messageText = 'This will be info about application' . PHP_EOL .
            'Instructions or so..' . PHP_EOL;

        $client->sendMessage([
            'chat_id' => $chat->getId(),
            'text' => $messageText,
        ]);
    }
}
