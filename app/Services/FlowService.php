<?php

namespace App\Services;

use App\Eloquent\Customer;
use App\Events\States\Answered;
use App\Events\States\AskedAgain;
use App\Events\States\ChosenDir;
use App\Events\States\ChosenLang;
use App\Events\States\Finished;
use App\Events\States\Start;
use Telegram\Bot\Api;
use Telegram\Bot\Objects\Update;

/**
 * Class FlowService
 * @package App\Services
 * @property Api $telegram
 */
class FlowService
{

    public function processUpdate(Update $update)
    {

        $message = $update->getMessage();
        $chat = $message->getChat();
        $chatId = $message->getChat()->getId();
        $text = $message->getText();
        $firstName = $chat->getFirstName();
        $lastName = $chat->getLastName();
        $userName = $chat->getUsername();

        /** @var Customer $customer */
        if (!$customer = Customer::query()->where('chat_id', $chatId)->first()) {
            $customer = $this->addCustomer($chatId, $userName, $firstName, $lastName, Customer::STATE_START);
        }

        if ($customer->update_id && $customer->update_id >= $update->getUpdateId()) {
            return;
        }

        switch ($customer->state) {
            case Customer::STATE_START: event(new Start($update, $customer));
                break;
            case Customer::STATE_CHOOSING_LANGUAGE: event(new ChosenLang($update, $customer));
                break;
            case Customer::STATE_CHOOSING_DIRECTION: event(new ChosenDir($update, $customer));
                break;
            case Customer::STATE_ANSWERING: event(new Answered($update, $customer));
                break;
            case Customer::STATE_FINISHED: event(new Finished($update, $customer));
                break;
            case Customer::STATE_ASKED_AGAIN: event(new AskedAgain($update, $customer));
                break;
        }

    }

    /**
     * @param $chatId
     * @param $userName
     * @param $firstName
     * @param $lastName
     * @param $state
     * @return Customer
     */
    private function addCustomer($chatId, $userName, $firstName, $lastName, $state): Customer
    {
        if (!$customer = Customer::query()->where('chat_id', $chatId)->first()) {
            $customer = new Customer();
            $customer->chat_id = $chatId;
            $customer->state = $state;
            $customer->username = $userName;
            $customer->first_name = $firstName;
            $customer->last_name = $lastName;
            $customer->save();
        }

        return $customer;
    }

}
