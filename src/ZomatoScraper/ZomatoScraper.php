<?php namespace Jdecano;
use Scraper\Scrape\Extractor\Types\MultipleRowExtractor;
use Scraper\Scrape\Crawler\Types\GeneralCrawler;
class ZomatoScraper {
	/**
	 * [$base description]
	 * @var string
	 */
	private $base = 'https://www.zomato.com';
	/**
	 * [$city description]
	 * @var string
	 */
	 private $city;
	 /**
	  * [$urls description]
	  * @var array
	  */
	 private $urls;
	 /**
	  * [$data description]
	  * @var array
	  */
	 private $data;
	/**
	 * @param  string $city
	 * @return void
	 */
	public function __construct($city) {
		$this->base = 'https://www.zomato.com/melbourne/steak-ministry-bar-grill-glen-waverley';
	}
	public function getTotalPages() {
		$extractor = new \Scraper\Scrape\Extractor\Types\MultipleRowExtractor(new GeneralCrawler($this->base),__DIR__."/pages.json");
		return $extractor->extract();
	}
	/**
	 * @return mixed
	 */
	public function process() {
		$pages =  $this->getTotalPages();
		echo var_dump($pages);
	}
}