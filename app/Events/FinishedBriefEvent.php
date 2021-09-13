<?php

namespace App\Events;

use App\Eloquent\Customer;

class FinishedBriefEvent
{
    public $customer;
    public $filledHere;

    public function __construct(Customer $customer, bool $filledHere)
    {
        $this->customer = $customer;
        $this->filledHere = $filledHere;
    }
}
