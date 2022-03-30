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

    $log = [
      'ip' => $result[1],
      'identity' => $result[2],
      'user' => $result[3],
      'date' => $result[4],
      'time' => $result[5],
      'timezone' => $result[6],
      'method' => $result[7],
      'url' => $result[8],
      'protocol' => $result[9],
      'status' => $result[10],
      'traffic' => $result[11],
      'referer' => $result[12],
      'agent' => $result[13]
    ];

    $views ++;

    if($log['method'] == 'POST')
    {
      $traffic += $log['traffic'];
    }

    if(!in_array($log['url'], $urls))
    {
      $urls[] = $log['url'];
      $uniqueUrls++;
    }

    if(array_key_exists($log['status'], $statusCodes))
    {
      $statusCodes[$log['status']] ++;
    }
    else
    {
      $statusCodes[$log['status']] = 1;
    }
    
    if (preg_match($googlePattern, $log['agent']))
    {
      $crawlers['Google'] ++;
    }
    else if (preg_match($bingPattern, $log['agent']))
    {
      $crawlers['Bing'] ++;
    }
    else if (preg_match($baiduPattern, $log['agent']))
    {
      $crawlers['Baidu'] ++;
    }
    else if (preg_match($yandexPattern, $log['agent']))
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
    echo json_encode($output,JSON_PRETTY_PRINT);
?>