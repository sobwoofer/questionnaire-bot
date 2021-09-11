<?php

namespace App\Listeners\States;

use App\Eloquent\Customer;
use App\Events\States\Finished;
use App\Events\States\Start;
use Telegram\Bot\Api;
use Telegram\Bot\Objects\Chat;

class FinishedListener
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
    public function handle(Finished $event): void
    {
        $message = $event->update->getMessage();
        $this->client->sendChatAction(['chat_id' => $message->getChat()->getId(), 'action' => 'typing']);

        $this->proposeMenu($message->getChat(), $event->customer->language);

        $event->customer->setUpdateId($event->update->getUpdateId());
        $event->customer->setState(Customer::STATE_ASKED_AGAIN);
    }

    private function proposeMenu(Chat $chat, string $language): void
    {
        if ($language == Customer::LANG_EN) {
            $text = 'Do you want to answer again?';
            $keyboard = [['Yes', 'No thanks']];
        } else {
            $text = 'Хотите ли вы ответить на вопросы сначала?';
            $keyboard = [['Да', 'Нет спасибо']];
        }

        $reply_markup = $this->client->replyKeyboardMarkup([
            'keyboard' => $keyboard,
            'resize_keyboard' => true,
            'one_time_keyboard' => true,
        ]);

        $this->client->sendMessage([
            'chat_id' => $chat->getId(),
            'text' => $text,
            'reply_markup' => $reply_markup,
        ]);
    }

}
