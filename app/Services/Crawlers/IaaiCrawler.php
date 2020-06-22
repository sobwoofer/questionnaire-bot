<?php

namespace App\Services\Crawlers;

use App\Eloquent\CustomerCred;
use App\Eloquent\CustomerFilter;
use Facebook\WebDriver\WebDriverBy;
use Symfony\Component\DomCrawler\Crawler;

class IaaiCrawler
{
    /**
     * @param CustomerFilter $filter
     * @param bool $browser
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function crawl(CustomerFilter $filter, $browser = false): array
    {
        $filterUrl = urldecode($filter->filter_url);

        if (!$browser) {
            $items = $this->crawlByCurl($filterUrl);
        } else {
            $items = $this->crawlByBrowser($filterUrl, $filter->customer->id);
        }

        return $items;
    }

    /**
     * @param string $filterUrl
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function crawlByCurl(string $filterUrl): array
    {
        $client = new \GuzzleHttp\Client();
        $response = $client->request('GET', $filterUrl);

        $code = $response->getStatusCode(); // 200
        $header = $response->getHeaderLine('content-type'); // 'application/json; charset=utf8'
        $body = (string)$response->getBody(); // '{"id": 1420053, "name": "guzzle", ...}'

        $crawler = new Crawler();
        $crawler->addHtmlContent($body);

        $items = $crawler->filter('.table-row')->each(function (Crawler $node, $i) {
            return [
                'image' => $node->filter('.table-cell--img img')->attr('src'),
                'url' => $node->filter('.heading-7 a.link')->attr('href'),
                'title' => $node->filter('.heading-7 a.link')->text(),
                'description' => $node->filter('.table-cell--actions p')->first()->text()
            ];
        });

        return $items;
    }

    public function crawlByBrowser(string $filterUrl, int $customerId): array
    {
        $loginUrl = 'https://www.iaai.com/Login/LoginPage?ReturnUrl=/mydashboard/default';
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

        $cred = CustomerCred::query()
            ->where('customer_id', $customerId)
            ->where('spot_type', 'iaai')
            ->first();
        $screenPath = 'storage/app/public/screens/';
        if ($cred) {
            $client->request('GET', $loginUrl);
            $client->findElement(WebDriverBy::cssSelector('.login-input-container #txtUserName'))->sendKeys($cred->login);
            $client->findElement(WebDriverBy::cssSelector('.login-input-container input[type="password"]'))->sendKeys($cred->password);
            $client->takeScreenshot($screenPath . 'write' . time() . '.png');
            $client->findElement(WebDriverBy::cssSelector('.login-input-container button[type="button"]'))->click();
            $client->takeScreenshot($screenPath . 'login' . time() . '.png');
            sleep(2);
        }

        $client->request('GET', $filterUrl);
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

        return $items;
    }

}
