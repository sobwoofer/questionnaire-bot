<?php

namespace App\Listeners\States;

use App\Eloquent\Customer;
use App\Eloquent\CustomerAnswer;
use App\Eloquent\Question;
use App\Events\FinishedBriefEvent;
use App\Events\States\Answered;
use Telegram\Bot\Api;

class AnsweredListener
{
    private $client;

    public function __construct(Api $client)
    {
        $this->client = $client;
    }

    /**
     * @throws \Telegram\Bot\Exceptions\TelegramSDKException
     */
    public function handle(Answered $event): void
    {
        $message = $event->update->getMessage();
        $this->client->sendChatAction(['chat_id' => $message->getChat()->getId(), 'action' => 'typing']);

        $answerState = $event->customer->answer_state;

        /** @var Question $answeredQuestion */
        $answeredQuestion = Question::query()->where('position', $answerState)->first();

        $answerPosition = $answerState + 1;

        $this->addCustomerAnswer($message->getText(), $event->customer->id, $answeredQuestion->id);

        $langColumn = 'question_' . $event->customer->language;
        $nextQuestion = Question::query()->where('position', $answerPosition)->first();

        if (!$nextQuestion) {
            //TODO event send all customer answers to admin
            $event->customer->setState(Customer::STATE_FINISHED);
            $finishText = Question::where('role', '=', Question::ROLE_FINAL)->first();

            $event->customer->setAnswerState(null);
            $this->client->sendMessage(['chat_id' => $message->getChat()->getId(), 'text' => $finishText->$langColumn,]);
            $event->customer->setUpdateId($event->update->getUpdateId());

            event(new FinishedBriefEvent($event->customer, true));
            return;
        }

        $event->customer->setAnswerState($answerPosition);
        $this->client->sendMessage(['chat_id' => $message->getChat()->getId(), 'text' => $nextQuestion->$langColumn,]);
        $event->customer->setUpdateId($event->update->getUpdateId());
    }

    private function addCustomerAnswer(string $answer, int $customerId, int $questionId): void
    {
        $customerAnswer = new CustomerAnswer();
        $customerAnswer->customer_id = $customerId;
        $customerAnswer->question_id = $questionId;
        $customerAnswer->answer = $answer;
        $customerAnswer->save();
    }
}
