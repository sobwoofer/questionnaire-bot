<?php

namespace App\Events\States;

use App\Eloquent\Customer;
use Telegram\Bot\Api;
use Telegram\Bot\Objects\Update;

/**
 * Class Start
 * @property Update $update
 * @property Customer $customer
 * @property Api $telegramApiClient
 * @package App\Events\States
 */
class Start
{
    public $update;
    public $customer;
    public $telegramApiClient;

    public function __construct(Update $update, Customer $customer, Api $telegramApiClient)
    {
        $this->update = $update;
        $this->customer = $customer;
        $this->telegramApiClient = $telegramApiClient;
    }
}
