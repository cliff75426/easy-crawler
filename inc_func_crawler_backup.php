<?php

  class crawler_cls
  {
    public $url;
    public $url_arr = array();
    public $url_queue = array();
    public $file;
    function __construct($url,$url_arr,$url_queue,$file){
      $this->url=$url;
      $this->url_arr=$url_arr;
      $this->url_queue=$url_queue;
      $this->file=$file;
      var_dump($this->url_queue);
    }

    /****
     *the function is check Http_code status
     * @params : url
     * @output : true => work, false => notwork
     *
     *
     ***/
    private function check_status($link)
    {
      $link_headers = @get_headers($link);
      if(!$link_headers || $link_headers[0] == 'HTTP/1.1 404 Not Found') {
        return false;
      }else{
        return true;
      }
    }

    public function get_link($url)
    {
      $input = @file_get_contents($url);
      $regexp = "<a\s[^>]*href=([^\"]|[^\'])([^(\')|(\") >]*?)[^>]*>(.*)<\/a>";
      $base_url = parse_url($url, PHP_URL_HOST);
      preg_match_all("/$regexp/siU",$input,$matches);
      $l = $matches[2];
      foreach($l as $link)
      {

        if(substr($link,-1)=="\""||substr($link,-1)=="\'"){$link=substr($link,0,-1);}
        if(strpos($link, "#"))
        {
            $link = substr($link, 0, strpos($link, "#"));
        }

        if(substr($link,0,1) == ".")
        {
            $link = substr($link, 1);
        }

        if(substr($link,0 , 7) == "http://")
        {
            $link = $link;
        }else if (substr($link, 0, 8)== "https://"){
            $link = $link;
        }else if(substr($link, 0, 2)== "//"){
            $link = substr($link, 2);
        }else if(substr($link, 0, 1) == "#"){
            $link = $url;
        }else if(substr($link, 0, 7)== "mailto:"){
            $link = "[".$link."]";
        }else{
            if(substr($link, 0, 1) == "/")
            {
              $link = $base_url."".$link;
              //$link = $base_url."/".$link;
            }else{
              $link = $base_url.$link;
            }
        }

        if(substr($link, 0, 7) != "http://" && substr($link, 0, 8) != "https://" && substr($link, 0, 1) != "[")
        {
            if(substr($url, 0, 8) == "https://")
            {
                $link = "https://".$link;
            }else{
                $link = "http://".$link;
            }
        }
        if(!crawler_cls::check_path_category("newspapers",$link)){continue;}

        if(crawler_cls::check_status($link)==true)
        {
       //   echo "OK:  ".$link.PHP_EOL;
          if(crawler_cls::check_in_pool($this->url_arr,$link)==true)
          {
            continue;
          }else{
            $this->url_arr[md5($link)]="$link";
            array_push($this->url_queue,$link);
          }
        }else{
         // echo "NOT: ".$link.PHP_EOL;
        }
      }
      //return $this->url_queue;
    }


    private function check_in_pool($array_pool,$value){
      $index = md5($value);
      if(isset($array_pool[$index]))
      {
        return true;
      }else{
        return false;
      }
    }

    public function check_path_category($category,$link){
      $regex = "/([^\/][a-z0-9A-Z]*?)/siU";
      preg_match($regex,parse_url($link,PHP_URL_PATH),$matches);
      $match_word=$matches[0];
      if($match_word==$category){
        return true;
      }else{
        return false;
      }
    }

    public function start_crawler($times){
      array_push($this->url_queue,$this->url);

      for($i=0;$i<=$times;$i++){
        $this->get_link($this->url_queue[$i]);
        if(count($this->url_arr)>=$times){break;}
        echo count($this->url_arr).PHP_EOL;
        $this->record();
      }
      $this->record();
      var_dump($this->url_arr);
    }

    public function record(){
      $open_file = fopen($this->file,"w") or die("UNABLE TO OPEN FILE!");
      foreach($this->url_arr as $value){
        fwrite($open_file,$value."\n");
      }
      fclose($open_file);
    }

  }

$start_seed="http://www.chinatimes.com/";
$pool=array();
$queue=array();
for($i=2016;$i<2018;$i++){
  for($j=1;$j<=12;$j++){
    for($k=1;$k<=31;$k++){
      for($l=2601;$l<=2604;$l++){
        if($j<=9){$month="0".$j;}
        else{$month=$j;}
        if($k<=9){$day="0".$k;}
        else{$day=$k;}
        array_push($queue,"http://www.chinatimes.com/history-by-date/".$i."-".$month."-".$day."-".$l);
      }
    }
  }
}
var_dump($queue);
$file="test_2.txt";
$url=new crawler_cls($start_seed,$pool,$queue,$file);
echo $url->start_crawler(100);
