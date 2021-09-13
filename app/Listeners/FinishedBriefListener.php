<?php

namespace App\Listeners;

use App\Eloquent\Customer;
use App\Events\FinishedBriefEvent;
use Telegram\Bot\Api;

/**
 * Class FirstFilterCrawledListener
 * @package App\Listeners
 * @property Api $telegram
 */
class FinishedBriefListener
{
    protected const CUSTOMER_URL = '/admin/show/customer/';

    private $telegram;

    public function __construct(Api $telegram)
    {
        $this->telegram = $telegram;
    }

    /**
     * @param FinishedBriefEvent $event
     */
    public function handle(FinishedBriefEvent $event): void
    {
        $managers = Customer::query()->where('role', '=', Customer::ROLE_MANAGER)->get();

        if ($event->filledHere) {
            $url = url('/') . self::CUSTOMER_URL . $event->customer->id;
            $message = 'Got new brief: ' . PHP_EOL . $url . PHP_EOL;
        } else {
            $message = 'Got new brief via google forms by: ' . $event->customer->first_name . PHP_EOL;
        }

        /** @var Customer $manager */
        foreach ($managers as $manager) {
            $this->telegram->sendMessage([
                'chat_id' => $manager->chat_id,
                'text' => $message,
            ]);
        }

    }
}
