<?php

namespace App\Console\Commands;

use App\Eloquent\CustomerItem;
use App\User;
use Facebook\WebDriver\WebDriverBy;
use Illuminate\Console\Command;
use Symfony\Component\DomCrawler\Crawler;

class CheckAutoUpdates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check';

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
     * @throws \Facebook\WebDriver\Exception\NoSuchElementException
     * @throws \Facebook\WebDriver\Exception\TimeoutException
     */
    public function handle()
    {
        $screenPath = 'storage/app/public/screens/';
        $client = \Symfony\Component\Panther\Client::createChromeClient(null,
            [
                '--ignore-certificate-errors',
                '--allow-insecure-localhost',
                '--headless',
                '--window-size=1200,1100',
                '--disable-gpu',
                '--no-sandbox',
                '--no-sandbox',
                '--disable-dev-shm-usage',
            ],

            ['connection_timeout_in_ms' => 120000, 'request_timeout_in_ms' => 120000]
        );

        $login = 'https://www.iaai.com/Login/LoginPage?ReturnUrl=/mydashboard/default';
        $filter = 'https://www.iaai.com/VehicleSearch/SearchDetails?quickfilters=make:Volkswagen&url=6ozNTcDZf3LpsbcvtMBXrWzPnAG9oZTu1U72T1nrwgWerfEVKzUUHuszY9FSjziKg4tB9sJkhOB8QccMU/krY77dNBZFNElGwml8awr+4G2psgMnjU2qgncHBa3yQVHztXPzh8kMwRZ25/pQwge6zzCco1BqbfXhTifV1Um6eVdpYxgKiqbviDhQ0VLAy/U6NJCvlw5KiEtMZTpY+gydaPwchZCfOhhAE1DMtxnHrXfMB4Gf3nd1C1jlnXWscKADX59iVmo8QL8HT18zrLv88Qybaq8WUDYK3NXhGBCnonkbaOrEOiOZgnMY/W+qKWtf5yuN3XwiThbryHawApX6f0xwpw+Mkt5u/P+ZOBjhH6XUCd6KdR2YP0y8MLpaBdPH+34txPG2TlykaxiO6uSHUWfSYC05XXw1HLnztSYqU9s=';

//        $client->request('GET', $login);

//        $client->findElement(WebDriverBy::cssSelector('.login-input-container #txtUserName'))->sendKeys('sobwoofer8@gmail.com');
//        $client->findElement(WebDriverBy::cssSelector('.login-input-container input[type="password"]'))->sendKeys('ivan7207700');
//        $client->takeScreenshot($screenPath . 'write' . time() . '.png');
//        $client->findElement(WebDriverBy::cssSelector('.login-input-container button[type="button"]'))->click();

//        $client->takeScreenshot($screenPath . 'login' . time() . '.png');
//        $client->waitFor('.container-welcome-upgrade');
//        sleep(2);
        $client->request('GET', $filter);
        $client->waitFor('.table-advanced.table--image-view');
        sleep(2);
//        $client->takeScreenshot($screenPath . 'filter' . time() . '.png');

        $crawler = $client->getCrawler();
        $items = $crawler->filter('.table-row')->each(function (Crawler $node, $i) {
            return [
                'image' => $node->filter('.table-cell--img img')->attr('src'),
                'url' => $node->filter('.heading-7 a.link')->attr('href'),
                'title' => $node->filter('.heading-7 a.link')->text(),
                'description' => $node->filter('.table-cell--actions p')->first()->text()
            ];
        });

        foreach ($items as $item) {
           $customerItem = new CustomerItem();
            $customerItem->image = $item['image'];
            $customerItem->url = $item['url'];
            $customerItem->title = $item['title'];
            $customerItem->description = $item['description'];
        }

        $itemsP = $crawler->filter('.table-row')->each(function (Crawler $node, $i) {
            return $node->text('content');
        });


    }
}
