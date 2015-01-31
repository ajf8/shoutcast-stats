<?php 

class ShoutcastParser
{
  var $relay;
  var $response;
  var $version;

  function ShoutcastParser($relay, $response, $version)
  {
    $this->relay = $relay;
    $this->response = $response;
    $this->version = $version;
  }

  function GetTable($dom)
  {
    $table = array();
    $rows = $dom->getElementsByTagName("tr");
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
    return $table;
  }

  function GetStats($stats)
  {
    libxml_use_internal_errors(true);

    $dom = new DOMDocument;
    $dom->loadHTML($this->response);

    $stats->svers = $this->version;

    $table = $this->GetTable($dom);
    foreach($table as $key=>$val)
    {
      if ($key == 'Stream Status') {
        $stats->stream_status = $val;
        if (preg_match("/(\d+) of (\d+) listeners/", $val, $matches))
        {
          $stats->listeners = $matches[1];
          $stats->max_listeners = $matches[2];
          $stats->status = "up";
        }
      } elseif ($key == 'Server Status') {
        $stats->server_status = $val;
      } elseif ($key == 'Current Song') {
        $stats->song = $val;
      } elseif ($key == 'Listener Peak') {
        $stats->peak = $val;
      }
    }
  }
}

?>
