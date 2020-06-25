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
                'Извините не понимаю вас. Выберите что небуть из меню?'
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
        $keyboard = [[AddFilterListener::ACTION, ShowFiltersListener::ACTION, RemoveFilterListener::ACTION, InfoListener::ACTION]];
        $reply_markup = $client->replyKeyboardMarkup([
            'keyboard' => $keyboard,
            'resize_keyboard' => true,
            'one_time_keyboard' => true
        ]);

        $client->sendMessage([
            'chat_id' => $chat->getId(),
            'text' => $messageText ?: 'Привет, если вы хотите "охотится" за какими либо товарами - ' .
                                    'Вам сюда. Нажмите "Добавить фильтр" в меню и ждите обновлений.',
            'reply_markup' => $reply_markup
        ]);
    }

}
