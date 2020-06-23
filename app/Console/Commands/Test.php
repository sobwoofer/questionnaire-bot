<?php

namespace App\Console\Commands;

use App\Eloquent\Customer;
use App\Events\States\AddFilter;
use App\Events\States\Info;
use App\Events\States\RemoveFilter;
use App\Events\States\RunFilter;
use App\Events\States\ShowFilters;
use App\Events\States\StopFilter;
use App\Events\States\Start;
use App\Listeners\States\AddFilterListener;
use App\Listeners\States\InfoListener;
use App\Listeners\States\RemoveFilterListener;
use App\Listeners\States\ShowFiltersListener;
use App\Listeners\States\StartListener;
use App\Listeners\States\StopFilterListener;
use Illuminate\Console\Command;
use App\Events\States\Hunting;
use Telegram\Bot\Api;

/**
 * Class Test
 * @package App\Console\Commands
 * @property Api $telegram
 */
class Test extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    private $telegram;


    public function __construct(Api $telegram)
    {
        $this->telegram = $telegram;
        parent::__construct();
    }

    /**
     * @return string|void
     * @throws \Telegram\Bot\Exceptions\TelegramSDKException
     */
    public function handle()
    {
        $telegramApiClient = $this->telegram;
        $updates = $this->telegram->getUpdates();

        foreach ($updates as $update) {

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
                continue;
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
                case StopFilterListener::ACTION: event(new StopFilter($update, $customer, $telegramApiClient));
                    return;
                case InfoListener::ACTION: event(new Info($update, $customer, $telegramApiClient));
                    return;
            }

            switch ($customer->state) {
                case Customer::STATE_START: event(new Start($update, $customer, $telegramApiClient));
                    break;
                case Customer::STATE_ADD_FILTER: event(new AddFilter($update, $customer, $telegramApiClient));
                    break;
                case Customer::STATE_SHOW_FILTERS: event(new ShowFilters($update, $customer, $telegramApiClient));
                    break;
                case Customer::STATE_RUN_FILTER: event(new RunFilter($update, $customer, $telegramApiClient));
                    break;
                case Customer::STATE_REMOVE_FILTER: event(new RemoveFilter($update, $customer, $telegramApiClient));
                    break;
                case Customer::STATE_STOP_FILTER: event(new StopFilter($update, $customer, $telegramApiClient));
                    break;
                case Customer::STATE_HUNTING: event(new Hunting($update, $customer, $telegramApiClient));
                    break;
            }

        }

        return ';';
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
