<?php

namespace App\Console\Commands;

use App\Eloquent\CustomerFilter;
use App\Eloquent\CustomerItem;
use App\Events\FirstFilterCrawled;
use App\Services\Crawlers\IaaiCrawler;
use Illuminate\Console\Command;

class CrawlFilters extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crawl-filters';

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
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function handle(): void
    {
        $filters = CustomerFilter::query()->where('enabled', true)->get();
        $items = [];

        /** @var CustomerFilter $filter */
        foreach ($filters as $filter) {
            switch ($filter->spot_type) {
                case CustomerFilter::SPOT_IAAI:
                    $items = $this->checkFilterIaai($filter);
                    break;
                case CustomerFilter::SPOT_OLX:
                    $items = $this->checkFilterOlx($filter);
                    break;
                case CustomerFilter::SPOT_AUTORIA:
                    $items = $this->checkFilterAutoria($filter);
                    break;
            }
            $itemsExistBefore = $filter->items;

            foreach ($items as $item) {
                $customerItem = new CustomerItem();
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

    /**
     * @param CustomerFilter $filter
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function checkFilterIaai(CustomerFilter $filter): array
    {
        $crawler = new IaaiCrawler();
        return $crawler->crawl($filter);
    }

    private function checkFilterOlx(CustomerFilter $filter): array
    {
        $crawler = new IaaiCrawler();
        return [];
    }

    private function checkFilterAutoria(CustomerFilter $filter): array
    {
        return [];
    }

}
