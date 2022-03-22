<?php

  $googlePattern = '(Googlebot)';
  $bingPattern = '(msnbot)';
  $baiduPattern = '(Baiduspider)';
  $yandexPattern = '(YandexBot)';

  $logPattern = '/(\S+) (\S+) (\S+) \[([^:]+):(\d+:\d+:\d+) ([^\]]+)\] \"(\S+) (.*?) (\S+)\" (\S+) (\S+) (\".*?\") (\".*?\")/';

  $crawlers = [
    'Google' => 0,
    'Bing' => 0,
    'Baidu' => 0,
    'Yandex' => 0
  ];
  $uniqueUrls = 0;
  $traffic = 0;
  $views = 0;
  $urls = [];
  $statusCodes = [];

  $logFile = fopen('log_file.txt','r') or die('Не могу открыть файл');

  while(!feof($logFile)){ 
    $line = fgets($logFile);
    $result = [];
    preg_match($logPattern, $line, $result);

    $views ++;
    $traffic += $result[11];

    if(!in_array($result[12],$urls))
    {
      $uniqueUrls++;
      array_push($urls, $result[12]);
    }

    if(!array_key_exists($result[10],$statusCodes))
    {
      $statusCodes[$result[10]] = 1;
    }
    else
    {
      $statusCodes[$result[10]] += 1;
    }
    
    if (preg_match($googlePattern, $result[13]))
    {
      $crawlers['Google'] ++;
    }
    else if (preg_match($bingPattern, $result[13]))
    {
      $crawlers['Bing'] ++;
    }
    else if (preg_match($baiduPattern, $result[13]))
    {
      $crawlers['Baidu'] ++;
    }
    else if (preg_match($yandexPattern, $result[13]))
    {
      $crawlers['Yandex'] ++;
    }
  }
  $output = [
    'views' => $views,
    'urls' => $uniqueUrls,
    'traffic' => $traffic,
    'crawlers' => $crawlers,
    'statusCodes' => $statusCodes
  ];
  echo json_encode($output)
?>