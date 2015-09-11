<?php
/**
 * Created by PhpStorm.
 * User: jdecano
 * Date: 9/10/2015
 * Time: 5:05 AM
 */

namespace Jdecano;
use Goutte\Client;
use Jdecano\Exceptions\CityNotFoundException;
use League\Csv\Writer;
/**
 * Class ZomatoScraper
 * @package Jdecano
 */
class ZomatoScraper {

    /**
     * @var string $base;
     */
    private $base;

    /**
     * @var int $total
     */
    private $total;

    /**
     * @var array $pages
     */
    private $pages = [];

    /**
     * @var array
     */
    private $restaurants = [];

    /**
     * @var array
     */
    private $data = [];
    /**
     * [$path description]
     * @var string
     */
    private $path;

    /**
     * @param string $city
     * @param string $path
     */
    public function __construct($city = '', $path = '') {
        $this->setBase($city);
        $this->client = $this->setClient();
        $this->total = $this->getTotalPages();
        $this->pages = $this->getPages($this->total);
        $this->path = $path;
    }
    /**
     * @param array $data
     * @return void
     */
    private function write(array $data) {
        $writer = Writer::createFromPath(new SplFileObject($this->path, 'a+'), 'w');
        $writer->insertOne($data);
    }
    /**
     * @param $total
     * @return array
     */
    private function getPages($total) {

        $range = range(1, $total);

        $pages = [];

        foreach($range as $page) {
            $pages[] = $this->base.'?page='.$page;
        }

        return $pages;
    }

    /**
     * @return array
     */
    public function process() {

        $this->restaurants = $this->getRestaurants();
        $data = $this->data;
        foreach ($this->restaurants as $pages) {
            foreach ($pages as $page) {
                $data[] = $this->crawlRestaurant($page);
            }
        }

        $this->data = $data;
        return $this->data;
    }

    /**
     * @param $page
     * @return array
     */
    private function crawlRestaurant($page) {

        $data = [
            'name'      => '',
            'phone'     => '',
            'address'   => '',
            'known_for' => '',
            'opening_hours' => [],
            'photos' => []
        ];

        $crawler = $this->client->request('GET', $page);

        // Name
        $crawler->filterXPath("//h1[@class='res-name left']//span")->each(function ($node) use(&$data) {
            $data['name'] = $node->text();
        });

        // Phone
        $crawler->filterXPath("//div[@class='phone']//span[@class='tel']")->each(function ($node) use(&$data) {
            $data['phone'] = $node->text();
        });

        // Address
        $crawler->filterXPath('//*[@id="mainframe"]/div[1]/div/div[1]/div[1]/div[2]/div/div[4]/span[1]')->each(function ($node) use(&$data) {
            $data['address'] = strip_tags($node->text());
        });


        // Known For
        $crawler->filterXPath("//div[@class='res-info-known-for-text mr5']")->each(function ($node) use(&$data) {
            $data['known_for'] = strip_tags($node->text());
        });
        $counter = 0;
        $crawler->filterXPath('//*[@id="res-week-timetable"]/div/div[1]')->each(function ($node) use(&$data, &$counter) {
            $data['opening_hours'][$counter]['day'] = $node->text();
            $counter++;
        });
        $counter = 0;
        $crawler->filterXPath('//*[@id="res-week-timetable"]/div/div[2]/span')->each(function ($node) use(&$data, &$counter) {
            $data['opening_hours'][$counter]['hours'] = $node->text();
            $counter++;
        });

        // Known For
        $crawler->filterXPath('//*[@id="tabtop"]/div/div/div/div/div/a/img')->each(function ($node) use(&$data) {
            $data['photos'][] = $node->attr('data-original');
        });

        if ($this->path != '') {
            $this->writeToCsv($data);
        }

        return $data;
    }

    /**
     * @return array
     */
    private function getRestaurants() {

        $restaurants = [];

        foreach ($this->pages as $page) {
            $restaurants[] = $this->extractRestaurants($page);
        }

        //$restaurants[] = $this->extractRestaurants($this->pages[0]);

        return $restaurants;

    }

    /**
     * @param $page
     * @return array
     */
    private function extractRestaurants($page) {

        $restaurants = [];

        $crawler = $this->client->request('GET', $page);

        $crawler->filterXPath('//*[@id="orig-search-list"]/li/article/div/div/div/h3/a')->each(function ($node) use(&$restaurants) {
            $restaurants[] = $node->attr('href');
        });

        return $restaurants;

    }
    /**
     * @return Client
     */
    private function setClient() {
        $client = new Client();
        $guzzleClient = new \GuzzleHttp\Client(array(
            'curl' => array(
                CURLOPT_TIMEOUT => 60,
                CURLOPT_SSL_VERIFYPEER => false
            ),
        ));
        $client->setClient($guzzleClient);
        return $client;
    }

    /**
     * @param $city
     * @return $this
     * @throws CityNotFoundException
     */
    private function setBase($city) {
        $this->base = 'https://www.zomato.com/'.$city.'/restaurants';

        if (!$this->cityExists($this->base)) {
            throw new CityNotFoundException("Given city does not exists.");
        }

        return $this;
    }

    /**
     * @param $url
     * @return bool
     */
    private function cityExists($url) {
        $headers = @get_headers($url);
        if(strpos($headers[0],'200')===false)return false;
    }

    /**
     * @return float
     */
    private function getTotalPages() {

        $crawler = $this->client->request('GET', $this->base);

        $total = 0;

        $crawler->filterXPath('//*[@id="search-results-container"]/div[4]/div[1]/div')->each(function ($node) use(&$total) {
            $total = preg_replace("/Page\s\d\s(of)\s/", "", $node->text());
        });

        return ceil(intval($total) / 29);
    }


    /**
     * @return string
     */
    public function getBase()
    {
        return $this->base;
    }

    /**
     * @return int
     */
    public function getTotal()
    {
        return $this->total;
    }
}