<?php

$url="http://www.chinatimes.com/realtimenews/20180506002549-260402";

echo $url.PHP_EOL.PHP_EOL;

if(parse_url($url,PHP_URL_HOST)=="www.chinatimes.com"){
  echo "true".PHP_URL_HOST;
}

$regex="/([^\/][a-z0-9A-Z]*?)/siU";
preg_match($regex,parse_url($url,PHP_URL_PATH),$matches);
$match_word=$matches[0];
var_dump($matches);
echo $match_word;
if($match_word=="realtimenews"){
  echo "right_____right";
}
