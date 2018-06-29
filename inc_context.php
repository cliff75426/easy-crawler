<?php

$url = "http://www.chinatimes.com/realtimenews/20180507003674-260404";

function content_analysis($url){

  $regex_article = "/(<article class=\"arttext marbotm clear-fix)(.*?)(<\/article>)/s";
  $regex_content = "/<p>([^<].*)<\/p>/U"; //array[1]
  $regex_date = "/<time datetime=\"(.*)\">/U"; //aray[1]
  $regex_title = "/<title>(.*)<\/title>/U"; //array[1]
  $regex_keywords = "/<meta name=\"keywords\"(.*)content=\"(.*)\"/U";
  $file = file_get_contents($url);
  preg_match_all($regex_article,$file,$match_article);
  if($match_article[0][0]==null){
    $re_str = "@GSIC\n@link:".$url."\n@title:\n@keywords:\n@date:\n@content:\n";
    return $re_str;
  }else{
    preg_match_all($regex_content,$match_article[0][0],$match_content);
    preg_match_all($regex_date,$match_article[0][0],$match_date);
    preg_match_all($regex_title,$file,$match_title);
    preg_match_all($regex_keywords,$file,$match_keywords);
    $content = "";
    foreach($match_content[1] as $value){
      $content = $content.$value;
    }
    $re_str = "@GSIC\n@link:".$url."\n@title:".$match_title[1][0]."\n@keywords:".$match_keywords[2][0]."\n@date:".$match_date[1][0]."\n@content:".$content."\n";
    return $re_str;
  }
}

$count = 0;
$handle = fopen(__DIR__."/text/other/textak", "r");
if ($handle) {
    $fw_file = fopen(__DIR__."/text/other/result/11","w+");
    while (($line = fgets($handle)) !== false) {
      // process the line read.
      $count+=1;
      echo $count."\n";
      $line = str_replace(array("\r", "\n", "\r\n", "\n\r"), '', $line);
      $str = content_analysis($line);
      fwrite($fw_file,$str);
    }
    fclose($fw_file);
    fclose($handle);
} else {
    // error opening the file.
}
