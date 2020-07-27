<?php

namespace App\Listeners\States;

use App\Eloquent\Customer;
use App\Eloquent\CustomerFilter;
use App\Events\States\ShowFilters;
use Telegram\Bot\Api;
use Telegram\Bot\Objects\Chat;

/**
 * Class ShowFiltersListener
 * @package App\Listeners\States
 */
class ShowFiltersListener
{
    public const ACTION = 'Показати фільтри';

    /**
     * @param ShowFilters $event
     * @throws \Telegram\Bot\Exceptions\TelegramSDKException
     */
    public function handle(ShowFilters $event)
    {
        $message = $event->update->getMessage();
        $event->telegramApiClient->sendChatAction(['chat_id' => $message->getChat()->getId(), 'action' => 'typing']);

        if ($message->getText() === self::ACTION) {
            $this->sendFilters($event->telegramApiClient, $message->getChat(), $event->customer->id);
            $event->customer->setState(Customer::STATE_HUNTING);
        }

        $event->customer->setUpdateId($event->update->getUpdateId());
    }

    /**
     * @param Api $client
     * @param Chat $chat
     * @param int $customerId
     */
    private function sendFilters(Api $client, Chat $chat, int $customerId): void
    {
        $messageText = '';
        $filters = CustomerFilter::query()->where('customer_id', $customerId)->get();
        if (count($filters)) {
            /** @var CustomerFilter $filter */
            foreach ($filters as $filter) {
                $statusText = $filter->enabled ? 'enabled' : 'disabled';
                $messageText .= 'ID: ' . $filter->id . PHP_EOL .
                                'Фільтр: ' . $filter->title . PHP_EOL .
                                'Статус: ' . $statusText . PHP_EOL .
                                'Url: ' . urldecode($filter->filter_url) . PHP_EOL .
                                '--------------------------------------' . PHP_EOL;
            }
        } else {
            $messageText = 'У Вас немає покищо фільтрів.';
        }

        $client->sendMessage([
            'chat_id' => $chat->getId(),
            'text' => $messageText,
        ]);
    }
}
