<?php

include 'vendor/autoload.php';

$config = include 'config.php';

try {
    // auth
    $service = ZendGData\Spreadsheets::AUTH_SERVICE_NAME;
    $adapter = new Zend\Http\Client\Adapter\Curl();
    $httpClient = new ZendGData\HttpClient();
    $httpClient->setAdapter($adapter);
    // it use your email and password from config.php
    $client = ZendGData\ClientLogin::getHttpClient($config['email'], $config['password'], $service, $httpClient);
} catch (Zend_Gdata_App_AuthException $ae) {
    die("Error: ". $ae->getMessage() ."\nCredentials provided were email: [$config[email]] and password [$config[password]].\n");
}

$spreadsheetService = new ZendGData\Spreadsheets($client);
$query = new ZendGData\Spreadsheets\CellQuery();
// speadsheet key & worksheed number comes from config.php
$query->setSpreadsheetKey($config['speadsheetKey']);
$query->setWorksheetId($config['worksheetID']);

$urlsMatched = array(); // the urls, grabbed from google's speadsheet will be saved here
$cellFeed = $spreadsheetService->getCellFeed($query);
foreach ($cellFeed as $cellEntry) {
    $cell = $cellEntry->getCell();
    $col = $cell->getColumn();
    $row = $cell->getRow();
    // it gets only 2 first columns
    if ($col == 1 || $col == 2) {
        // getting the url from cell value and storing in $urlsMatched
        // the sitemap.xml will be generated later
        $cellVal = $cell->getInputValue();
        if (filter_var($cellVal, FILTER_VALIDATE_URL)) {
            $urlsMatched[$row][$col] = $cellVal;
        }
    }
}

// building sitemap.xml
echo <<<INIT
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/TR/xhtml11/xhtml11_schema.html">
INIT;
foreach ($urlsMatched as $pair) {
    // most of pages has an alternative page written on another laguge
    if (count($pair) === 2 && array_key_exists(1, $pair) && array_key_exists(2, $pair)) {
        echo <<<URL2

  <url>
    <loc>$pair[1]</loc>
    <xhtml:link rel="alternate" hreflang="en" href="$pair[2]"/>
    <xhtml:link rel="alternate" hreflang="ru" href="$pair[1]"/>
  </url>
  <url>
    <loc>$pair[2]</loc>
    <xhtml:link rel="alternate" hreflang="ru" href="$pair[1]"/>
    <xhtml:link rel="alternate" hreflang="en" href="$pair[2]"/>
  </url>
URL2;
    // but few pages don't has an alternative page
    } else if(count($pair) === 1 && array_key_exists(1, $pair) || array_key_exists(2, $pair)) {
        $url = array_key_exists(1, $pair) ? $pair[1] : $pair[2];
        echo <<<URL1

  <url>
    <loc>$url</loc>
  </url>
URL1;
    }
}
echo <<<ENDXML

</urlset>
ENDXML;
