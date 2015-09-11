<?php
require_once __DIR__.'/../../vendor/autoload.php';
require_once __DIR__.'/../ZomatoScraper.php';
// First Parameter is the city that you want to crawl 
// Second is limit
$scraper = new \Jdecano\ZomatoScraper('melbourne');
print_r($scraper->process());
exit;