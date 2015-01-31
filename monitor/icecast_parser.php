<?php 

class IcecastParser
{
  var $relay;
  var $response;
  var $version;

  function IcecastParser($relay, $response, $version)
  {
    $this->relay = $relay;
    $this->response = $response;
    $this->version = $version;
  }

  function GetStreamId()
  {
    $parsed = parse_url($this->relay->url);
    return $parsed['path'];
  }

  function GetTable($dom)
  {
    $table = array();
    $streamid = $this->GetStreamId();

    $divs = $dom->getElementsByTagName("div");

    foreach($divs as $div)
    {
      if ($div->getAttribute("class") != "roundcont") {
        continue;
      }
      $h3s = $div->getElementsByTagName("h3");
      if ($h3s->length < 1 || strpos($h3s->item(0)->nodeValue, "Mount Point : (" . $streamid . ")") === FALSE)
      {
        continue;
      }
      $rows = $div->getElementsByTagName("tr");
      foreach($rows as $row)
      {
        $cells = $row->getElementsByTagName("td");
        if ($cells->length == 2)
        {
          $key = $cells->item(0)->nodeValue;
          if (preg_match("/(.*):/", $key, $matches))
          {
            $val = $cells->item(1)->nodeValue;
            $table[$matches[1]] = $val;
          }
        }
      }
    }
    return $table;
  }

  function GetStats($stats)
  {
    libxml_use_internal_errors(true);

    $dom = new DOMDocument;
    $dom->loadHTML($this->response);

    $stats->svers = "Icecast2";

    $table = $this->GetTable($dom);
    foreach($table as $key=>$val)
    {
      if ($key == 'Current Listeners') {
        $stats->listeners = $val;
        $stats->status = "up";
      } elseif ($key == 'Current Song') {
        $stats->song = $val;
      } elseif ($key == 'Peak Listeners') {
        $stats->peak = $val;
      }
    }
  }
}

?>
