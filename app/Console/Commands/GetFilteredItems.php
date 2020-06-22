<?php

namespace App\Console\Commands;

use App\Eloquent\CustomerFilter;
use App\Eloquent\CustomerItem;
use App\Services\Crawlers\IaaiCrawler;
use Illuminate\Console\Command;

class GetFilteredItems extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get-filtered-items';

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
    public function handle()
    {
        $filters = CustomerFilter::all();
        $items = [];

        /** @var CustomerFilter $filter */
        foreach ($filters as $filter) {
            switch ($filter->spot_type) {
                case 'iaai':
                    $items = $this->checkFilterIaai($filter);
                    break;
                case 'olx':
                    $items = $this->checkFilterOlx($filter);
                    break;
                case 'autoria':
                    $items = $this->checkFilterAutoria($filter);
                    break;
            }
        }

        foreach ($items as $item) {
            $customerItem = new CustomerItem();
            $customerItem->image = $item['image'];
            $customerItem->url = $item['url'];
            $customerItem->title = $item['title'];
            $customerItem->description = $item['description'];
            $customerItem->filter_id = $filter->id;

            if ($filter->filter_title) {
                if (stripos($item['title'], $filter->filter_title) !== false) {
                    $customerItem->save();
                }
            } else {
                $customerItem->save();
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
