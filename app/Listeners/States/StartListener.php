<?php

namespace App\Listeners\States;

use App\Events\States\Start;
use Telegram\Bot\Api;
use Telegram\Bot\Objects\Chat;

/**
 * Class StartListener
 * @package App\Listeners\States
 */
class StartListener
{
    public const ACTION = '/start';

    /**
     * @param Start $event
     * @throws \Telegram\Bot\Exceptions\TelegramSDKException
     */
    public function handle(Start $event): void
    {
        $message = $event->update->getMessage();
        $event->telegramApiClient->sendChatAction(['chat_id' => $message->getChat()->getId(), 'action' => 'typing']);

        if ($message->getText() === self::ACTION) {
            $this->proposeMenu($event->telegramApiClient, $message->getChat());
        } else {
            $this->proposeMenu(
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
    private function proposeMenu(Api $client, Chat $chat, string $messageText = null): void
    {
        $keyboard = [[AddFilterListener::ACTION, ShowFiltersListener::ACTION, RemoveFilterListener::ACTION, 'Info']];
        $reply_markup = $client->replyKeyboardMarkup([
            'keyboard' => $keyboard,
            'resize_keyboard' => true,
            'one_time_keyboard' => true
        ]);

        $client->sendMessage([
            'chat_id' => $chat->getId(),
            'text' => $messageText ?: 'Hello, do you want to spy some products?' .
                                    'Chose "Add Filter in menu and wait for new products"',
            'reply_markup' => $reply_markup
        ]);
    }

}
