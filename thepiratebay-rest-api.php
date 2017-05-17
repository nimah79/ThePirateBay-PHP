<?php

/*
The Pirate Bay REST API
By NimaH79
http://nimatv.ir
*/

header('Content-Type: application/json');
if(isset($_GET['q'])) {
  $page = file_get_contents('https://thepiratebay.life/search/'.urlencode($_GET['q']).'/0/99/0');
  preg_match_all('/<div class="detName.*>(.*)<.*\n.*\n<a href="(.*)" title="D.*\n<font class="detDesc">Uploaded (.*), Size (.*), ULed by <a class="detDesc" href=".*" title=".*">.*<\/a><\/font>\n<\/td>\n<td align="right">(.*)<\/td>\n<td align="right">(.*)<\/td>/', $page, $links);
  $result = [];
  if($page[0] === '<') {
    exit('{"error":"nothing found"}');
  }
  for($i = 0; $i<count($links[0]); $i++) {
    $result[] = ['title' => $links[1][$i], 'magnet' => $links[2][$i], 'size' => str_replace('&nbsp;', ' ', $links[4][$i]), 'date' => str_replace('&nbsp;', ' ', $links[3][$i]), 'seeders' => $links[5][$i], 'leechers' => $links[6][$i]];
  }
  exit(json_encode($result));
}
exit('{"error":"parameter q is required"}');
?>