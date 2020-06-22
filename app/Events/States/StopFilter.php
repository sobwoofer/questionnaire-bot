<?php

namespace App\Events\States;

use App\Eloquent\Customer;
use Telegram\Bot\Api;
use Telegram\Bot\Objects\Update;

/**
 * Class StopFilter
 * @package App\Events\States
 * @property Update $update
 * @property Api $telegramApiClient
 * @property Customer $customer
 */
class StopFilter
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
