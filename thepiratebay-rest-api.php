<?php

/*
The Pirate Bay REST API
By NimaH79
http://nimatv.ir
*/

header('Content-Type: application/json');
if(isset($_GET['q'])) {
  $page = file_get_contents('https://thepiratebay.org/search/'.urlencode($_GET['q']).'/0/99/0');
  preg_match_all('/<div class="detName">.*<a href=".*" class="detLink" title=".*">(.*)<\/a>\n<\/div>\n<a href="(.*)" title="Download this torrent using magnet"><img src=".*\n.*<font class="detDesc">Uploaded (.*), Size (.*), ULed by <a class="detDesc" href=".*" title="Browse .*">.*<\/a><\/font>\n.*<\/td>\n.*<td align="right">(.*)<\/td>\n.*<td align="right">(.*)<\/td>/', $page, $links);
  $result = [];
  for($i = 0; $i<count($links[0]); $i++) {
    $result[] = ['title' => $links[1][$i], 'magnet' => $links[2][$i], 'size' => str_replace('&nbsp;', ' ', $links[4][$i]), 'date' => str_replace('&nbsp;', ' ', $links[3][$i]), 'seeders' => $links[5][$i], 'leechers' => $links[6][$i]];
  }
  if($result === []) {
    exit('{"error":"nothing found"}');
  }
  exit(json_encode($result));
}
exit('{"error":"parameter q is required"}');
?>
