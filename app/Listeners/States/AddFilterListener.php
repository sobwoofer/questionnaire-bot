<?php

namespace App\Listeners\States;

use App\Eloquent\Customer;
use App\Eloquent\CustomerFilter;
use App\Events\States\AddFilter;
use Telegram\Bot\Api;
use Telegram\Bot\Objects\Chat;

/**
 * Class AddFilterListener
 * @package App\Listeners\States
 * @property Api $telegram
 */
class AddFilterListener
{
    public const ACTION = 'Додати фільтр';

    private $telegram;

    public function __construct(Api $telegram)
    {
        $this->telegram = $telegram;
    }

    /**
     * @param AddFilter $event
     * @throws \Telegram\Bot\Exceptions\TelegramSDKException
     */
    public function handle($event): void
    {
        $message = $event->update->getMessage();
        $event->telegramApiClient->sendChatAction(['chat_id' => $message->getChat()->getId(), 'action' => 'typing']);

        if ($message->getText() === self::ACTION) {
            $this->proposeSendFilterUrl($event->telegramApiClient, $message->getChat());
            $event->customer->setState(Customer::STATE_ADD_FILTER);
        } elseif ($event->customer->state === Customer::STATE_ADD_FILTER) {
            if (($spotType = $this->whichUrl($message->getText())) && $this->isUrlValid($message->getText())) {
                $this->addCustomerFilter($message->getText(), $spotType, $event->customer->id);
                $this->proposeAddFilterTitle($message->getChat());
                $event->customer->setState(Customer::STATE_ADD_FILTER_TITLE);
            } else {
                $this->sayUrlNotValid($event->telegramApiClient, $message->getChat());
            }
        } elseif ($event->customer->state === Customer::STATE_ADD_FILTER_TITLE) {
            if ($filter = $this->getNewFilter($event->customer->id)) {
                $this->addFilterTitle($message->getText(), $filter);
                $this->sayFilterAdded($message->getChat());
                $event->customer->setState(Customer::STATE_HUNTING);
            } else {
                $this->sayFilterNotFound($message->getChat());
            }
        }

        $event->customer->setUpdateId($event->update->getUpdateId());
    }

    /**
     * @param Api $client
     * @param Chat $chat
     * @param string $messageText
     */
    private function proposeSendFilterUrl(Api $client, Chat $chat, string $messageText = null): void
    {
        $client->sendMessage([
            'chat_id' => $chat->getId(),
            'text' => $messageText ?: 'Пришліть URL сайту з відфільтрованим товаром на який ви хочете полювати',
        ]);
    }

    private function proposeAddFilterTitle(Chat $chat, string $messageText = null): void
    {
        $this->telegram->sendMessage([
            'chat_id' => $chat->getId(),
            'text' => $messageText ?: 'Давайте назвемо ваш фільтр, напишіть назву.',
        ]);
    }

    /**
     * @param Chat $chat
     */
    private function sayFilterNotFound(Chat $chat): void
    {
        $this->telegram->sendMessage([
            'chat_id' => $chat->getId(),
            'text' => 'Не можу знайти фільтра. Створіть ще раз нажавши кнопку ' . Customer::STATE_ADD_FILTER,
        ]);
    }

    /**
     * @param Api $client
     * @param Chat $chat
     * @param string|null $messageText
     */
    private function sayUrlNotValid(Api $client, Chat $chat, string $messageText = null): void
    {
        $client->sendMessage([
            'chat_id' => $chat->getId(),
            'text' => $messageText ?: 'Вибачте, але нажаль ця ссилка не знайома для мене. Я вмію працювати тільки з ' .
                CustomerFilter::SPOT_AUTORIA . ', ' . CustomerFilter::SPOT_IAAI . ', ' . CustomerFilter::SPOT_OLX
                . PHP_EOL .
                ' Відправте будь ласка ссилку на фільтр одного з вищевказаних сайтів',
        ]);
    }

    /**
     * @param Chat $chat
     */
    private function sayFilterAdded(Chat $chat): void
    {
        $this->telegram->sendMessage([
            'chat_id' => $chat->getId(),
            'text' => 'Фільтр успішно доданий',
        ]);
    }

    /**
     * @param string $url
     * @return bool
     */
    private function isUrlValid(string $url): bool
    {
        return filter_var($url, FILTER_VALIDATE_URL);
    }

    /**
     * @param string $url
     * @return null|string
     */
    private function whichUrl(string $url): ?string
    {
        if (stripos($url, 'https://' . CustomerFilter::SPOT_OLX) === 0 || stripos($url, CustomerFilter::SPOT_OLX) === 0) {
            return CustomerFilter::SPOT_OLX;
        }

        if (stripos($url, 'https://' . CustomerFilter::SPOT_IAAI) === 0 || stripos($url, CustomerFilter::SPOT_IAAI) === 0) {
            return CustomerFilter::SPOT_IAAI;
        }

        if (stripos($url, 'https://' . CustomerFilter::SPOT_AUTORIA) === 0 || stripos($url, CustomerFilter::SPOT_AUTORIA) === 0) {
            return CustomerFilter::SPOT_AUTORIA;
        }

        return null;
    }

    /**
     * @param string $url
     * @param string $spotType
     * @param int $customerId
     * @return bool
     */
    private function addCustomerFilter(string $url, string $spotType, int $customerId): bool
    {
        $customerFilter = new CustomerFilter();
        $customerFilter->filter_url = urlencode($url);
        $customerFilter->spot_type = $spotType;
        $customerFilter->customer_id = $customerId;
        $customerFilter->enabled = false;
        return $customerFilter->save();
    }

    private function getNewFilter(
        int $customerId,
        $titleNull = true,
        $scheduleNull = true,
        $enabled = false
    ): ?CustomerFilter
    {
        $query =  CustomerFilter::query();
        if ($titleNull) {
            $query->where('title', null);
        }

        /** @var CustomerFilter $filter */
        $filter = $query->where('customer_id', $customerId)->where('enabled', $enabled)->first();

        return $filter;
    }

    private function addFilterTitle(string $title, CustomerFilter $filter): bool
    {
        /** @var CustomerFilter $filter */
        $filter->title = $title;
        $filter->enabled = true;
        $filter->schedule = '12 hours';
        return $filter->save();
    }
}
