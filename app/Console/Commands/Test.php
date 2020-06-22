<?php

namespace App\Console\Commands;

use App\Eloquent\Customer;
use App\Events\States\AddFilter;
use App\Events\States\RemoveFilter;
use App\Events\States\RunFilter;
use App\Events\States\ShowFilters;
use App\Events\States\StopFilter;
use App\Events\States\Start;
use Illuminate\Console\Command;
use App\Events\States\Hunting;
use Telegram\Bot\Api;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Laravel\TelegramServiceProvider;

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

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @return string|void
     * @throws \Telegram\Bot\Exceptions\TelegramSDKException
     */
    public function handle()
    {
        $telegramApiClient = new Api('652236963:AAH3cyoQASEhyuaeao-MAWjbKZCmsjK1Czk');
        $updates = $telegramApiClient->getUpdates();

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
                $this->addCustomer($chatId, $userName, $firstName, $lastName, Customer::START_STATE);
            }

            if ($customer->update_id && $customer->update_id >= $update->getUpdateId()) {
                continue;
            }

            switch ($customer->state) {
                case Customer::START_STATE: event(new Start($update, $customer, $telegramApiClient));
                    break;
                case Customer::ADD_FILTER_STATE: event(new AddFilter($update, $customer, $telegramApiClient));
                    break;
                case Customer::SHOW_FILTERS_STATE: event(new ShowFilters($update, $customer, $telegramApiClient));
                    break;
                case Customer::RUN_FILTER_STATE: event(new RunFilter($update, $customer, $telegramApiClient));
                    break;
                case Customer::REMOVE_FILTER_STATE: event(new RemoveFilter($update, $customer, $telegramApiClient));
                    break;
                case Customer::STOP_FILTER_STATE: event(new StopFilter($update, $customer, $telegramApiClient));
                    break;
                case Customer::HUNTING_STATE: event(new Hunting($update, $customer, $telegramApiClient));
                    break;
            }

        }



        return ';';
    }

    private function addCustomer($chatId, $userName, $firstName, $lastName, $state): void
    {
        if (!Customer::query()->where('chat_id', $chatId)->first()) {
            $customer = new Customer();
            $customer->chat_id = $chatId;
            $customer->state = $state;
            $customer->username = $userName;
            $customer->first_name = $firstName;
            $customer->last_name = $lastName;
            $customer->save();
        }
    }
}
