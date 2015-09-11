<?php
require_once __DIR__.'/../../vendor/autoload.php';
require_once __DIR__.'/../ZomatoScraper.php';
// First Parameter is the city that you want to crawl 
// Second is limit
$scraper = new \Jdecano\ZomatoScraper('melbourne', 1);
// Process still returns the whole array of data
header('Content-Type: application/json');
echo json_encode($scraper->process());