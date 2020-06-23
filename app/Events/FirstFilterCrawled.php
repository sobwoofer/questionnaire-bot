<?php

namespace App\Events;

use App\Eloquent\CustomerFilter;

/**
 * Class FirstFilterCrawled
 * @package App\Events\States
 * @property int itemsCount
 * @property CustomerFilter $filter
 */
class FirstFilterCrawled
{
    public $itemsCount;
    public $filter;

    public function __construct(int $itemsCount, CustomerFilter $filter)
    {
        $this->itemsCount = $itemsCount;
        $this->filter = $filter;
    }
}
