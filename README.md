# ZomatoCraper
Scrapes zomato website

```
// First Parameter is the city that you want to crawl 
// Seond Parameter is the csv file path
$scraper = new \Jdecano\ZomatoScraper('melbourne', __DIR__.'/example.csv');
// Process still returns the whole array of data
$scraper->process();
exit;
```
