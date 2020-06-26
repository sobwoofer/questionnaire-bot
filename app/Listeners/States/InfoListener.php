<?php

namespace App\Listeners\States;

use App\Eloquent\Customer;
use App\Eloquent\CustomerFilter;
use App\Events\States\Info;
use App\Events\States\ShowFilters;
use Telegram\Bot\Api;
use Telegram\Bot\Objects\Chat;

/**
 * Class InfoListener
 * @package App\Listeners\States
 */
class InfoListener
{
    public const ACTION = 'Справка';

    /**
     * @param Info $event
     * @throws \Telegram\Bot\Exceptions\TelegramSDKException
     */
    public function handle(Info $event): void
    {
        $message = $event->update->getMessage();
        $event->telegramApiClient->sendChatAction(['chat_id' => $message->getChat()->getId(), 'action' => 'typing']);

        if ($message->getText() === self::ACTION) {
            $this->sendInfo($event->telegramApiClient, $message->getChat());
            $event->customer->setState(Customer::STATE_HUNTING);
        }

        $event->customer->setUpdateId($event->update->getUpdateId());
    }

    /**
     * @param Api $client
     * @param Chat $chat
     */
    private function sendInfo(Api $client, Chat $chat): void
    {
        $messageText = '-- Для чого це все?'
            . PHP_EOL .
            'Мене створили для пошуку товарів які покищо не з\'явились в інтернет магазинах або на інших площадках.'
            . PHP_EOL .
            '-- Як це працює? '
            . PHP_EOL .
            'Уявіть ви шукаєте для себе б.у. Iphone X 256Gb. ви зайшли на OLX, відфільтрували свій товар та обдзвонили усі ' .
            'пропозиції, але на жаль ніодна вас не влаштовує. Отже я вам маякну відразу коли на OLX з\'явиться ще один айфончик ' .
            'з вашими параметрами.'
            . PHP_EOL .
            '-- Що потрібно зробити? '
            . PHP_EOL .
            'Просто скопіюйте ссилку з вашого браузеру після того як ви налаштували фільтр під ваші вимоги, пришліть її мені '.
            'і займайтесь своїми справами поки я спостерігатиму чи не з\'явилось часом нове оголошення з вашим ' .
            'айфончиком на olx.ua, або ж з автомобілем на auto.ria.com, або навіть на аукціоні IAAI.com в США.'
            . PHP_EOL .
            'Instructions or so..' . PHP_EOL;

        $client->sendMessage([
            'chat_id' => $chat->getId(),
            'text' => $messageText,
        ]);
    }
}
