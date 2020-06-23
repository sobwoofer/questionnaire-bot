<?php

namespace App\Console\Commands;

use App\Eloquent\CustomerFilter;
use App\Eloquent\CustomerItem;
use App\Events\FreshItemsFound;
use Carbon\Carbon;
use Illuminate\Console\Command;
use DateInterval;
use Log;

class CheckFilterUpdates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check-filter-updates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @throws \Exception
     */
    public function handle()
    {
        $filters = CustomerFilter::query()->where('enabled', true)->get();

        /** @var CustomerFilter $filter */
        foreach ($filters as $filter) {
            if ($newLinks = $this->getFreshCreatedItems($filter->id, $filter->schedule)) {
                event(new FreshItemsFound($newLinks, $filter));
            }
        }
    }

    /**
     * @param int $filterId
     * @param string $schedule
     * @return array
     * @throws \Exception
     */
    private function getFreshCreatedItems(int $filterId, string $schedule): array
    {
        if (!$lastCheckDate = CustomerItem::query()->orderBy('created_at', 'desc')->pluck('created_at')->first()) {
            throw new \Exception('cant get lastCheckDate');
        }

        /** @var Carbon $lastCheckDate */
        $previousCheckDate = $lastCheckDate->clone()->sub(DateInterval::createFromDateString($schedule));
        $beforePreviousCheckDate = $previousCheckDate->clone()->sub(DateInterval::createFromDateString($schedule));

        $lastLinkPack = CustomerItem::betweenDatesByFilterId($filterId, $previousCheckDate, $lastCheckDate)->pluck('url')->all();
        $lastLinkPack = array_unique($lastLinkPack);

        $previousLinkPack = CustomerItem::betweenDatesByFilterId($filterId, $beforePreviousCheckDate, $previousCheckDate)->pluck('url')->all();
        $previousLinkPack = array_unique($previousLinkPack);

        Log::info('check-item-updates Found lastLinkPack: ' . count($lastLinkPack)
            . ' and previousLinkPack: ' . count($previousLinkPack));

        $freshLinks = [];
        foreach ($lastLinkPack as $lastUrl) {
            if (!in_array($lastUrl, $previousLinkPack, true)) {
                $freshLinks[] = $lastUrl;
            }
        }

        Log::info('check-item-updates found fresh links: ' . count($freshLinks));

        return $freshLinks;
    }
}
