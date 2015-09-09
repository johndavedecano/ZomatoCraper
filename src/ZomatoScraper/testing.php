<?php
require_once __DIR__.'/../../vendor/autoload.php';
require_once __DIR__.'/ZomatoScraper.php';

$scraper = new \Jdecano\ZomatoScraper('melbourne');
$scraper->process();