<?php

namespace App\Console\Commands;

use App\Eloquent\Question;
use App\Eloquent\CustomerAnswer;
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
    public function handle(): void
    {
        $filters = Question::query()->where('enabled', true)->get();
        $message = 'filters found ' . count($filters) . PHP_EOL;
        $this->info($message);
        Log::info($message);
        /** @var Question $filter */
        foreach ($filters as $filter) {
            $message = 'processing filter id ' . $filter->id. PHP_EOL;
            $this->info($message);
            Log::info($message);
            if ($newLinks = $this->getFreshCreatedItems($filter->id, $filter->schedule)) {
                $message = 'neq links found' . count($newLinks) . PHP_EOL;
                $this->info($message);
                Log::info($message);
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
        if (!$lastCheckDate = CustomerAnswer::query()->orderBy('created_at', 'desc')->pluck('created_at')->first()) {
            throw new \Exception('cant get lastCheckDate');
        }

        /** @var Carbon $lastCheckDate */
        $previousCheckDate = $lastCheckDate->clone()->sub(DateInterval::createFromDateString($schedule));
        $beforePreviousCheckDate = $previousCheckDate->clone()->sub(DateInterval::createFromDateString($schedule));

        $lastLinkPack = CustomerAnswer::betweenDatesByFilterId($filterId, $previousCheckDate, $lastCheckDate)->pluck('url')->all();
        $lastLinkPack = array_unique($lastLinkPack);

        $previousLinkPack = CustomerAnswer::betweenDatesByFilterId($filterId, $beforePreviousCheckDate, $previousCheckDate)->pluck('url')->all();
        $previousLinkPack = array_unique($previousLinkPack);

        Log::info('check-item-updates Found lastLinkPack: ' . count($lastLinkPack)
            . ' and previousLinkPack: ' . count($previousLinkPack));

        $freshLinks = [];
        if ($previousLinkPack) {
            foreach ($lastLinkPack as $lastUrl) {
                if (!in_array($lastUrl, $previousLinkPack, true)) {
                    $freshLinks[] = $lastUrl;
                }
            }
        }

        Log::info('check-item-updates found fresh links: ' . count($freshLinks));

        return $freshLinks;
    }
}
