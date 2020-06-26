<?php

namespace App\Listeners\States;

use App\Eloquent\Customer;
use App\Eloquent\CustomerFilter;
use App\Events\States\RemoveFilter;
use Telegram\Bot\Api;
use Telegram\Bot\Objects\Chat;

/**
 * Class RemoveFilterListener
 * @package App\Listeners\States
 * @property Api $telegram
 */
class RemoveFilterListener
{
    public const ACTION = 'Видалити фильтр';
    private $telegram;
    public function __construct(Api $telegram)
    {
        $this->telegram = $telegram;
    }

    /**
     * @param RemoveFilter $event
     * @throws \Telegram\Bot\Exceptions\TelegramSDKException
     */
    public function handle(RemoveFilter $event)
    {
        $message = $event->update->getMessage();
        $event->telegramApiClient->sendChatAction(['chat_id' => $message->getChat()->getId(), 'action' => 'typing']);

        if ($message->getText() === self::ACTION) {
            $this->proposeSetFilterId($message->getChat());
            $event->customer->setState(Customer::STATE_REMOVE_FILTER);
        } elseif($filter = $this->validateFilter((int)$message->getText(), $event->customer->id)) {
            $this->deleteFilter((int)$message->getText(), $event->customer->id);
            $this->sayDeletedSuccess($message->getChat());
            $event->customer->setState(Customer::STATE_HUNTING);
        } else {
            $this->sayIdNotFound($message->getChat());
        }

        $event->customer->setUpdateId($event->update->getUpdateId());
    }

    /**
     * @param int $filterId
     * @param int $customerId
     * @return
     */
    private function validateFilter(int $filterId, int $customerId)
    {
        return CustomerFilter::query()
            ->where('customer_id', $customerId)
            ->where('id', $filterId)
            ->first();
    }

    private function deleteFilter(int $filterId, int $customerId): void
    {
        CustomerFilter::query()
            ->where('customer_id', $customerId)
            ->where('id', $filterId)
            ->delete();
    }

    /**
     * @param Chat $chat
     */
    private function proposeSetFilterId(Chat $chat): void
    {
        $this->telegram->sendMessage([
            'chat_id' => $chat->getId(),
            'text' => 'Відправте мені ID фільтру який хочете видалити, ' .
            'Ви можете подивитися ID вашого фільтру нажавши кнопку ' . ShowFiltersListener::ACTION,
        ]);
    }

    /**
     * @param Chat $chat
     */
    private function sayIdNotFound(Chat $chat): void
    {
        $this->telegram->sendMessage([
            'chat_id' => $chat->getId(),
            'text' => 'Не можу знайти фільтр з таким ID. ' .
                'Ви можете подивитися ID вашого фільтру нажавши кнопку ' . ShowFiltersListener::ACTION,
        ]);
    }

    /**
     * @param Chat $chat
     */
    private function sayDeletedSuccess(Chat $chat): void
    {
        $this->telegram->sendMessage([
            'chat_id' => $chat->getId(),
            'text' => 'Фільтр видалений.',
        ]);
    }

}
