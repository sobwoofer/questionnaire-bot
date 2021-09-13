<?php

namespace App\Listeners\States;

use App\Eloquent\Customer;
use App\Eloquent\Question;
use App\Events\States\ChosenLang;
use Telegram\Bot\Api;

class ChosenLangListener
{
    public const LANG_EN = 'English';
    public const LANG_RU = 'Русский';

    private $client;

    public function __construct(Api $client)
    {
        $this->client = $client;
    }

    /**
     * @param ChosenLang $event
     * @throws \Telegram\Bot\Exceptions\TelegramSDKException
     */
    public function handle(ChosenLang $event): void
    {
        $message = $event->update->getMessage();
        $this->client->sendChatAction(['chat_id' => $message->getChat()->getId(), 'action' => 'typing']);

        $event->customer->setLang($message->getText() === self::LANG_EN ? Customer::LANG_EN : Customer::LANG_RU);

        $langColumn = 'question_' . $event->customer->language;
        $dirQuestion = Question::query()->where('role', '=', Question::ROLE_DIRECTION)->first();

        $this->send($message->getChat()->getId(), $dirQuestion->$langColumn, $event->customer->language);

        $event->customer->setState(Customer::STATE_CHOOSING_DIRECTION);
        $event->customer->setUpdateId($event->update->getUpdateId());
    }

    public function send(string $chatId, string $text, string $lang)
    {
        $keyboard = [[
            $lang === Customer::LANG_EN ? 'Here' : 'Здесь',
            $lang === Customer::LANG_EN ? 'Doc' : 'Документ',
        ]];

        $reply_markup = $this->client->replyKeyboardMarkup([
            'keyboard' => $keyboard,
            'resize_keyboard' => true,
            'one_time_keyboard' => true,
        ]);

        $this->client->sendMessage([
            'chat_id' => $chatId,
            'text' => $text,
            'reply_markup' => $reply_markup,
        ]);
    }

}
