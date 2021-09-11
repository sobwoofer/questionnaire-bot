<?php

namespace App\Events;

use App\Eloquent\Question;

/**
 * Class FirstFilterCrawled
 * @package App\Events\States
 * @property int itemsCount
 * @property Question $filter
 */
class FirstFilterCrawled
{
    public $itemsCount;
    public $filter;

    public function __construct(int $itemsCount, Question $filter)
    {
        $this->itemsCount = $itemsCount;
        $this->filter = $filter;
    }
}
