<?php

namespace App\Listeners\States;

use App\Events\States\Hunting;
use Telegram\Bot\Api;
use Telegram\Bot\Objects\Chat;

/**
 * Class HuntingListener
 * @package App\Listeners\States
 */
class HuntingListener
{
    /**
     * @param Hunting $event
     * @throws \Telegram\Bot\Exceptions\TelegramSDKException
     */
    public function handle(Hunting $event)
    {
        $message = $event->update->getMessage();
        $event->telegramApiClient->sendChatAction(['chat_id' => $message->getChat()->getId(), 'action' => 'typing']);

        $this->proposeMenu(
            $event->telegramApiClient,
            $message->getChat(),
            'Вибачте не можу зрозуміти вас. Виберіть що небуть з меню.'
        );

        $event->customer->setUpdateId($event->update->getUpdateId());
    }

    private function proposeMenu(Api $client, Chat $chat, string $messageText = null): void
    {
        $keyboard = [[AddFilterListener::ACTION, ShowFiltersListener::ACTION, RemoveFilterListener::ACTION, InfoListener::ACTION]];
        $reply_markup = $client->replyKeyboardMarkup([
            'keyboard' => $keyboard,
            'resize_keyboard' => true,
            'one_time_keyboard' => true
        ]);

        $client->sendMessage([
            'chat_id' => $chat->getId(),
            'text' => $messageText,
            'reply_markup' => $reply_markup
        ]);
    }
}
