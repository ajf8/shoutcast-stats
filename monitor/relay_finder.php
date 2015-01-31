<?php

include_once "relay.php";

class RelayFinder
{
  var $url;

  function RelayFinder($url)
  {
    $this->url = $url;
  }

  function FindRelays()
  {
    $relays = array();
    
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $this->url);
#    curl_setopt($ch, CURLOPT_VERBOSE, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/40.0.2214.91 Safari/537.36");

    $response = curl_exec($ch);

    if (curl_getinfo($ch, CURLINFO_HTTP_CODE) == 200)
    {
      $comment = NULL;

      foreach(explode("\n", $response) as $line)
      {
        if (preg_match("/^#(.*)/", $line, $matches))
        {
          $comment = preg_replace("/^EXTINF:-1,::/", '', rtrim($matches[1]));
          continue;
        } elseif (preg_match("/^http/", $line)) {
          $relay = new Relay(rtrim($line));
          $relay->comment = $comment;
          array_push($relays, $relay);
        }
        $comment = NULL;
      }
    }
 
    curl_close($ch);

    return $relays;
  }
}

?>
