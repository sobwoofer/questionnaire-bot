<?php

namespace App\Listeners\States;

use App\Eloquent\Customer;
use App\Events\States\Start;
use Telegram\Bot\Api;
use Telegram\Bot\Objects\Chat;

class StartListener
{
    private $client;

    public function __construct(Api $client)
    {
        $this->client = $client;
    }

    /**
     * @param Start $event
     * @throws \Telegram\Bot\Exceptions\TelegramSDKException
     */
    public function handle(Start $event): void
    {
        $message = $event->update->getMessage();
        $this->client->sendChatAction(['chat_id' => $message->getChat()->getId(), 'action' => 'typing']);

        $this->proposeMenu($message->getChat());
        $event->customer->setUpdateId($event->update->getUpdateId());
        $event->customer->setState(Customer::STATE_CHOOSING_LANGUAGE);
    }

    private function proposeMenu(Chat $chat): void
    {
        $keyboard = [[ChosenLangListener::LANG_RU, ChosenLangListener::LANG_EN]];

        $reply_markup = $this->client->replyKeyboardMarkup([
            'keyboard' => $keyboard,
            'resize_keyboard' => true,
            'one_time_keyboard' => true,
        ]);

        $this->client->sendMessage([
            'chat_id' => $chat->getId(),
            'text' => 'Выберите язык. | Choose language.',
            'reply_markup' => $reply_markup,
        ]);
    }

}
