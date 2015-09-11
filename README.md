# ZomatoCraper
Scrapes zomato website

```
// First Parameter is the city that you want to crawl 
// Second is limit
$scraper = new \Jdecano\ZomatoScraper('melbourne', 10);
// Process still returns the whole array of data
header('Content-Type: application/json');
echo json_encode($scraper->process());
```
