<?php

/*
The Pirate Bay REST API
By NimaH79
http://nimah79.ir
*/

header('Content-Type: application/json');

if (isset($_REQUEST['q'])) {
    $page = file_get_contents('https://thepiratebay.org/search/'.urlencode($_GET['q']).'/0/99/0');

    preg_match_all('/<div class="detName">.*?<a href="(.*?)" class="detLink" title=".*?">(.*?)<\/a>\n<\/div>\n<a href="(.*?)" title="Download this torrent using magnet"><img src=".*?" alt="Magnet link" \/><\/a>.*?<img src=".*?>\n.*?<font class="detDesc">Uploaded (.*?), Size (.*?), ULed by <a class="detDesc" href=".*?" title="Browse .*?">(.*?)<\/a><\/font>\n.*?<\/td>\n.*?<td align="right">(.*?)<\/td>\n.*?<td align="right">(.*?)<\/td>/', $page, $links);
    $result = [];

    for ($i = 0; $i < count($links[0]); $i++) {
        preg_match('/\/torrent\/(.*?)\//', $links[1][$i], $id);
        $id = $id[1];
        $size = str_replace('&nbsp;', '', $links[5][$i]);
        $size = str_replace('MiB', 'MB', $size);
        $size = str_replace('GiB', 'GB', $size);
        $size = str_replace('KiB', 'KB', $size);
        $added = str_replace('&nbsp;', ' ', $links[4][$i]);
        $added = str_replace(' ', '-', $added);
        $result[] = ['id' => (int) $id, 'title' => $links[2][$i], 'detail_url' => 'https://thepiratebay.org'.$links[1][$i], 'author' => $links[6][$i], 'size' => $size, 'magnet' => $links[3][$i], 'seeders' => (int) $links[7][$i], 'leechers' => (int) $links[8][$i], 'added' => $added];
    }

    if (empty($result)) {
        exit('{"error":"nothing found"}');
    }

    exit(json_encode($result));
}

exit('{"error":"parameter q is required"}');
