<?php

namespace App\Console\Commands;

use App\Eloquent\Question;
use App\Eloquent\CustomerAnswer;
use App\Events\FirstFilterCrawled;
use App\Services\Crawlers\IaaiCrawler;
use App\Services\Crawlers\OlxCrawler;
use Illuminate\Console\Command;
use Log;

/**
 * Class CrawlFilters
 * @package App\Console\Commands
 * @property IaaiCrawler $iaaiCrawler
 * @property OlxCrawler $olxCrawler
 */
class CrawlFilters extends Command
{

    protected $signature = 'crawl-filters';
    protected $description = 'Command description';

    private $iaaiCrawler;
    private $olxCrawler;

    public function __construct(IaaiCrawler $iaaiCrawler, OlxCrawler $olxCrawler)
    {
        $this->iaaiCrawler = $iaaiCrawler;
        $this->olxCrawler = $olxCrawler;
        parent::__construct();
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function handle(): void
    {
        $filters = Question::query()->where('enabled', true)->get();
        $items = [];

        /** @var Question $filter */
        foreach ($filters as $filter) {
            $message = 'crawling filter id ' . $filter->id. PHP_EOL;
            $this->info($message);
            Log::info($message);
            switch ($filter->spot_type) {
                case Question::SPOT_IAAI:
                    $items = $this->iaaiCrawler->crawl($filter);
                    break;
                case Question::SPOT_OLX:
                    $items = $this->olxCrawler->crawl($filter);
                    break;
                case Question::SPOT_AUTORIA:
                    $items = $this->checkFilterAutoria($filter);
                    break;
            }
            $itemsExistBefore = $filter->items;

            $message = 'crawled items ' . count($items). PHP_EOL;
            $this->info($message);
            Log::info($message);

            foreach ($items as $item) {
                $customerItem = new CustomerAnswer();
                $customerItem->image = $item['image'];
                $customerItem->url = $item['url'];
                $customerItem->title = $item['title'];
                $customerItem->description = $item['description'];
                $customerItem->filter_id = $filter->id;
                $customerItem->save();
            }

            if (!count($itemsExistBefore)) {
                event(new FirstFilterCrawled(count($items), $filter));
            }

        }
    }


    private function checkFilterOlx(Question $filter): array
    {
        return [];
    }

    private function checkFilterAutoria(Question $filter): array
    {
        return [];
    }

}
