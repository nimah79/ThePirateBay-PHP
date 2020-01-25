<?php

define('SITE_BASE_URL', 'https://tpb.party');

/**
 * The Pirate Bay Wrapper
 * By NimaH79
 * http://nimah79.ir
 */

function xpathQuery($query, $html) {
    $use_errors = libxml_use_internal_errors(true);
    if (empty($query) || empty($html)) {
        return false;
    }
    $dom = new DomDocument();
    $dom->loadHTML($html);
    $xpath = new DomXPath($dom);
    $results = $xpath->query($query);
    libxml_use_internal_errors($use_errors);

    return $results;
}

function searchTorrents($keyword) {
    $page = curl_get_contents(SITE_BASE_URL.'/search/'.urlencode($keyword));
    $titles = xpathQuery('//table[@id="searchResult"]//tr/td[2]/div/a', $page);
    $result = array_fill(0, count($titles), []);
    for ($i = 0; $i < count($titles); ++$i) {
        $result[$i]['title'] = $titles[$i]->nodeValue;
    }
    $infos = xpathQuery('//table[@id="searchResult"]//tr/td[2]/font', $page);
    for ($i = 0; $i < count($infos); ++$i) {
        preg_match('/Uploaded (.*?), Size (.*?),/', $infos[$i]->nodeValue, $info);
        $result[$i]['date'] = preg_replace('/\x{00a0}/u', ' ', $info[1]);
        $result[$i]['size'] = preg_replace('/\x{00a0}/u', ' ', $info[2]);
    }
    $urls = xpathQuery('//table[@id="searchResult"]//tr/td[2]/div/a/@href', $page);
    for ($i = 0; $i < count($urls); ++$i) {
        $result[$i]['url'] = $urls[$i]->nodeValue;
    }
    $authors = xpathQuery('//table[@id="searchResult"]//tr/td[2]/font/a', $page);
    for ($i = 0; $i < count($infos); ++$i) {
        $result[$i]['author'] = $authors[$i]->nodeValue;
    }
    $author_urls = xpathQuery('//table[@id="searchResult"]//tr/td[2]/font/a/@href', $page);
    for ($i = 0; $i < count($infos); ++$i) {
        $result[$i]['author_url'] = $author_urls[$i]->nodeValue;
    }
    $magnets = xpathQuery('//table[@id="searchResult"]//tr/td[2]/a[starts-with(@href, "magnet")]/@href', $page);
    for ($i = 0; $i < count($magnets); ++$i) {
        $result[$i]['magnet'] = $magnets[$i]->nodeValue;
    }
    
    return $result;
}

function curl_get_contents($url) {
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false
    ]);
    $response = curl_exec($ch);
    curl_close($ch);

    return $response;
}
