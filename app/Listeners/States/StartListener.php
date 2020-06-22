<?php

namespace App\Listeners\States;

use App\Eloquent\Customer;
use App\Events\States\Start;
use Telegram\Bot\Api;
use Telegram\Bot\Objects\Chat;

/**
 * Class StartListener
 * @package App\Listeners\States
 */
class StartListener
{
    /**
     * @param Start $event
     * @throws \Telegram\Bot\Exceptions\TelegramSDKException
     */
    public function handle(Start $event): void
    {
        $message = $event->update->getMessage();
        $event->telegramApiClient->sendChatAction(['chat_id' => $message->getChat()->getId(), 'action' => 'typing']);

        switch ($message->getText()) {
            case '/start': $this->proposeAddFilter($event->telegramApiClient, $message->getChat());
                break;
            case 'Add Filter Url':
                $this->proposeSendFilterUrl($event->telegramApiClient, $message->getChat());
                $event->customer->setState(Customer::ADD_FILTER_STATE);
                break;
            default: $this->proposeAddFilter(
                $event->telegramApiClient,
                $message->getChat(),
                'Sorry dont understand you. Do you want to add filter url?'
            );
        }

        $event->customer->setUpdateId($event->update->getUpdateId());
    }

    /**
     * @param Api $client
     * @param Chat $chat
     * @param string $messageText
     */
    private function proposeAddFilter(Api $client, Chat $chat, string $messageText = null): void
    {
        $keyboard = [['Add Filter Url', 'No thanks']];
        $reply_markup = $client->replyKeyboardMarkup([
            'keyboard' => $keyboard,
            'resize_keyboard' => true,
            'one_time_keyboard' => true
        ]);

        $client->sendMessage([
            'chat_id' => $chat->getId(),
            'text' => $messageText ?: 'Hello, do you want to spy some products?',
            'reply_markup' => $reply_markup
        ]);
    }

    /**
     * @param Api $client
     * @param Chat $chat
     * @param string $messageText
     */
    private function proposeSendFilterUrl(Api $client, Chat $chat, string $messageText = null): void
    {
        $client->sendMessage([
            'chat_id' => $chat->getId(),
            'text' => $messageText ?: 'Grade :) send me please url with products which i will hunt',
        ]);
    }

}
