<?php

namespace App\Events\States;

use App\Eloquent\Customer;
use Telegram\Bot\Api;
use Telegram\Bot\Objects\Update;

/**
 * Class ChosenLanguage
 * @package App\Events\States
 * @property Update $update
 * @property Api $telegramApiClient
 * @property Customer $customer
 */
class ChosenLang
{
    public $update;
    public $customer;

    public function __construct(Update $update, Customer $customer)
    {
        $this->update = $update;
        $this->customer = $customer;
    }
}
