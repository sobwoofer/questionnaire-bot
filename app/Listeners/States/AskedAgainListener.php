<?php

namespace App\Listeners\States;

use App\Eloquent\Customer;
use App\Events\States\AskedAgain;
use App\Events\States\Start;
use Telegram\Bot\Api;

class AskedAgainListener
{
    private $client;

    public function __construct(Api $client)
    {
        $this->client = $client;
    }


    /**
     * @throws \Telegram\Bot\Exceptions\TelegramSDKException
     */
    public function handle(AskedAgain $event): void
    {
        $message = $event->update->getMessage();
        $this->client->sendChatAction(['chat_id' => $message->getChat()->getId(), 'action' => 'typing']);

        if ($message->getText() === 'Yes' || $message->getText() === 'Да') {
            event(new Start($event->update, $event->customer));
            return;
        }

        $event->customer->setUpdateId($event->update->getUpdateId());
        $event->customer->setState(Customer::STATE_FINISHED);
    }


}
