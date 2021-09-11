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

        if ($message->getText() === self::LANG_EN) {
            $event->customer->setLang('en');
        } else {
            $event->customer->setLang('ru');
        }

        $langColumn = 'question_' . $event->customer->language;
        $nextQuestion = Question::where('position', '=', 1)->first();

        $this->send($message->getChat()->getId(), $nextQuestion->$langColumn);

        $event->customer->setAnswerState(1);
        $event->customer->setState(Customer::STATE_ANSWERING);
        $event->customer->setUpdateId($event->update->getUpdateId());
    }

    public function send($chatId, $text)
    {
        $reply_markup = $this->client->replyKeyboardMarkup([
            'remove_keyboard' => true
        ]);

        $this->client->sendMessage([
            'chat_id' => $chatId,
            'text' => $text,
            'reply_markup' => $reply_markup,
        ]);
    }

    private function getQuestion(string $lang, int $position)
    {
        return Question::query()->where('position', $position)->first('question_' . $lang);
    }
}
