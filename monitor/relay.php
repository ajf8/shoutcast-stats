<?php

include_once "relay_stats.php";
include_once "parser_factory.php";

class Relay
{
  var $url;
  var $server;

  function Relay($url) 
  {
    $this->url = $url;
  }

  function UrlWithoutPath()
  {
    $parsed = parse_url($this->url);
    $res = $parsed['scheme'] . "://" . $parsed['host'];
    if (isset($parsed['port'])) {
      $res = $res . ":" . $parsed['port'];
    }
    return $res;
  }

  function HandleHeaderLine($ch, $header)
  {
    if (strpos($header, "Server:") === 0)
    {
      $this->server = $header;
    }
    return strlen($header);
  }

  function GetStats()
  {

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $this->UrlWithoutPath());
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
#    curl_setopt($ch, CURLOPT_VERBOSE, true);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/40.0.2214.91 Safari/537.36");
    curl_setopt($ch, CURLOPT_HEADERFUNCTION, array(&$this, 'HandleHeaderLine'));
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);

    $response = curl_exec($ch);
 
    $stats = new RelayStats($this);
    $stats->http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if ($stats->http_status == 200)
    {
      $parser_factory = new ParserFactory();
      $parser = $parser_factory->GetParser($this, $response);

      if (isset($parser))
      {
        $parser->GetStats($stats);
      }
      else
      {
        $stats->status = "unparseable";
      }
    } elseif ($errno = curl_errno($ch)) {
      $stats->full_output = curl_strerror($errno);
    }

    if (!isset($stats->status))
    {
      $stats->status = "down";
    }

    if ($stats->status != "up")
    {
      if (isset($response) && !empty($response)) {
        $stats->full_output = $response;
      } elseif ($errno = curl_errno($ch)) {
        $stats->full_output = curl_strerror($errno);
      }
    }

    curl_close($ch);  

    return $stats;
  }   
} 

?>
