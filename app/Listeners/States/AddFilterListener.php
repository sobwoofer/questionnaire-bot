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
 */
class AddFilterListener
{
    /**
     * @param AddFilter $event
     * @throws \Telegram\Bot\Exceptions\TelegramSDKException
     */
    public function handle(AddFilter $event): void
    {
        $message = $event->update->getMessage();
        $event->telegramApiClient->sendChatAction(['chat_id' => $message->getChat()->getId(), 'action' => 'typing']);

        if (($spotType = $this->whichUrl($message->getText())) && $this->isUrlValid($message->getText())) {
            $this->addCustomerFilter($message->getText(), $spotType, $event->customer->id);
            $this->sayUrlSuccessAdded($event->telegramApiClient, $message->getChat(), $spotType);
            $event->customer->setState(Customer::HUNTING_STATE);
        } else {
            $this->sayUrlNotValid($event->telegramApiClient, $message->getChat());
        }

        $event->customer->setUpdateId($event->update->getUpdateId());
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
            'text' => $messageText ?: 'Sorry but seems your url is not valid. I works only with ' .
                CustomerFilter::SPOT_AUTORIA . ', ' . CustomerFilter::SPOT_IAAI . ', ' . CustomerFilter::SPOT_OLX .
                ' Send please one of these filter url or go back',
        ]);
    }

    /**
     * @param Api $client
     * @param Chat $chat
     * @param string $spotType
     */
    private function sayUrlSuccessAdded(Api $client, Chat $chat, string $spotType): void
    {
        $client->sendMessage([
            'chat_id' => $chat->getId(),
            'text' => 'Congratulations, your url type is ' . $spotType . '. I will check for new products every day.',
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
     * @param string|null $filterTitle
     * @param string|null $title
     * @return bool
     */
    private function addCustomerFilter(
        string $url,
        string $spotType,
        int $customerId,
        string $filterTitle = null,
        string $title = null
    ): bool
    {
        $customerFilter = new CustomerFilter();
        $customerFilter->filter_url = urlencode($url);
        $customerFilter->spot_type = $spotType;
        $customerFilter->filter_title = $filterTitle;
        $customerFilter->customer_id = $customerId;
        $customerFilter->title = $title;
        $customerFilter->schedule = '1 day';
        return $customerFilter->save();
    }
}
