<?php

namespace App\Events\States;

use App\Eloquent\Customer;
use Telegram\Bot\Api;
use Telegram\Bot\Objects\Update;

class Answered
{
    public $update;
    public $customer;

    public function __construct(Update $update, Customer $customer)
    {
        $this->update = $update;
        $this->customer = $customer;
    }
}
