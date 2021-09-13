<?php

namespace App\Listeners\States;

use App\Eloquent\Customer;
use App\Eloquent\Question;
use App\Events\FinishedBriefEvent;
use App\Events\States\ChosenDir;
use Telegram\Bot\Api;

class ChosenDirectionListener
{
    private $client;

    public function __construct(Api $client)
    {
        $this->client = $client;
    }

    /**
     * @param ChosenDir $event
     * @throws \Telegram\Bot\Exceptions\TelegramSDKException
     */
    public function handle(ChosenDir $event): void
    {
        $message = $event->update->getMessage();
        $this->client->sendChatAction(['chat_id' => $message->getChat()->getId(), 'action' => 'typing']);

        $langColumn = 'question_' . $event->customer->language;

        if (in_array($message->getText(), ['Doc', 'Документ'])) {

            $finishText = Question::where('role', '=', Question::ROLE_FINAL)->first();
            $this->send($message->getChat()->getId(), $finishText->$langColumn);
            sleep(2);
            $docUrl = Question::where('role', '=', Question::ROLE_DOC)->first();
            $this->send($message->getChat()->getId(), $docUrl->$langColumn);

            $event->customer->setState(Customer::STATE_FINISHED);
            $event->customer->setUpdateId($event->update->getUpdateId());
            event(new FinishedBriefEvent($event->customer, false));

            return;
        }

        $nextQuestion = Question::query()->where('position', '=', 1)
                                        ->where('role', '=', Question::ROLE_QUESTION)->first();
        $this->send($message->getChat()->getId(), $nextQuestion->$langColumn);

        $event->customer->setAnswerState(1);
        $event->customer->setState(Customer::STATE_ANSWERING);
        $event->customer->setUpdateId($event->update->getUpdateId());
    }

    public function send($chatId, $text)
    {
        $reply_markup = $this->client->replyKeyboardMarkup(['remove_keyboard' => true]);

        $this->client->sendMessage([
            'chat_id' => $chatId,
            'text' => $text,
            'reply_markup' => $reply_markup,
        ]);
    }

}
