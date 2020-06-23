<?php

namespace App\Events\States;

use App\Eloquent\Customer;
use Telegram\Bot\Api;
use Telegram\Bot\Objects\Update;

/**
 * Class Info
 * @package App\Events\States
 * @property Update $update
 * @property Api $telegramApiClient
 * @property Customer $customer
 */
class Info
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
