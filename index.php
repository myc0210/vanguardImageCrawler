<?php
/**
 * Created by PhpStorm.
 * User: yunchengmao
 * Date: 2/4/15
 * Time: 9:10 PM
 */
require('vendor/autoload.php');
use Goutte\Client;

$client = new Client();
$crawler = $client->request('GET', 'http://www.northerncross-cs.com/');

//$targets = ['folder15','folder16','folder17','folder18'];
$targets = ['folder15'];
$links = [];
foreach ($targets as $target) {
    $crawler->filter('#' . $target . '>li>a')->each(function ($node) use (&$links) {
        $localClient = new Client();
        $localCrawler = $localClient->request('GET', $node->attr('href'));
        $localCrawler->filter('.photo_line_80>a')->each(function ($node) use (&$links) {
            $links[] = $node->attr('href');
        });
        $pagenationLinkCache = $localCrawler->filter('.sec_line_top>a');
        $count = 0;
        $end = count($pagenationLinkCache) - 1;
        $pagenationLinkCache->each(function ($node) use ($localClient, &$links, &$count, $end) {
            if ($count != $end) {
                $localCrawler = $localClient->request('GET', $node->attr('href'));
                $localCrawler->filter('.photo_line_80>a')->each(function ($node) use (&$links) {
                    $links[] = $node->attr('href');
                });
            }
        });
    });
}
var_dump($links);
echo '\n';
echo 'Step 1: finish.';
$images = [];

foreach ($links as $link) {
    $client = new Client();
    $crawler = $client->request('GET', $link);
    $crawler->filter('#main_img_href')->each(function ($node) use (&$images) {
        $images[] = $node->attr('href');
    });
}

var_dump($images);