<?php

namespace App\Events\States;

use App\Eloquent\Customer;
use Telegram\Bot\Api;
use Telegram\Bot\Objects\Update;

/**
 * Class Start
 * @property Update $update
 * @property Customer $customer
 * @package App\Events\States
 */
class Finished
{
    public $update;
    public $customer;
    public $telegramApiClient;

    public function __construct(Update $update, Customer $customer)
    {
        $this->update = $update;
        $this->customer = $customer;
    }
}
