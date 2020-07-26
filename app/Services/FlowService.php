<?php

namespace App\Services;

use App\Eloquent\Customer;
use App\Events\States\AddFilter;
use App\Events\States\Hunting;
use App\Events\States\Info;
use App\Events\States\RemoveFilter;
use App\Events\States\ShowFilters;
use App\Events\States\Start;
use App\Listeners\States\AddFilterListener;
use App\Listeners\States\InfoListener;
use App\Listeners\States\RemoveFilterListener;
use App\Listeners\States\ShowFiltersListener;
use App\Listeners\States\StartListener;
use Telegram\Bot\Api;
use Telegram\Bot\Objects\Update;

/**
 * Class FlowService
 * @package App\Services
 * @property Api $telegram
 */
class FlowService
{
    private $telegram;

    public function __construct(Api $telegram)
    {
        $this->telegram = $telegram;
    }

    public function processUpdate(Update $update)
    {
        $telegramApiClient = $this->telegram;

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

        switch ($text) {
            case StartListener::ACTION: event(new Start($update, $customer, $telegramApiClient));
                return;
            case ShowFiltersListener::ACTION: event(new ShowFilters($update, $customer, $telegramApiClient));
                return;
            case AddFilterListener::ACTION: event(new AddFilter($update, $customer, $telegramApiClient));
                return;
            case RemoveFilterListener::ACTION: event(new RemoveFilter($update, $customer, $telegramApiClient));
                return;
            case InfoListener::ACTION: event(new Info($update, $customer, $telegramApiClient));
                return;
        }

        switch ($customer->state) {
            case Customer::STATE_START: event(new Start($update, $customer, $telegramApiClient));
                break;
            case Customer::STATE_ADD_FILTER: event(new AddFilter($update, $customer, $telegramApiClient));
                break;
            case Customer::STATE_ADD_FILTER_TITLE: event(new AddFilter($update, $customer, $telegramApiClient));
                break;
            case Customer::STATE_SHOW_FILTERS: event(new ShowFilters($update, $customer, $telegramApiClient));
                break;
            case Customer::STATE_REMOVE_FILTER: event(new RemoveFilter($update, $customer, $telegramApiClient));
                break;
            case Customer::STATE_HUNTING: event(new Hunting($update, $customer, $telegramApiClient));
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
