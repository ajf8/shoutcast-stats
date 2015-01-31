<?php

class StreamStatsModel
{
  function GetStats()
  {
    $db = new mysqli('localhost', 'streamstats', '', 'streamstats') or die('Could not connect to DB: ' . mysql_error());
    $res = $db->query("SELECT id, dt_created, listeners FROM stat_runs WHERE active=1 ORDER BY dt_created DESC LIMIT 1");

    $run = $res->fetch_assoc();

    $relays_select = $db->prepare("SELECT url, comment, listeners, max_listeners, status, svers, peak, song, ROUND((listeners / max_listeners * 100)) AS pct FROM relay_stats WHERE run_id=? ORDER BY status, pct DESC");
    $relays_select->bind_param('d', $run['id']);
    $relays_select->execute();
    $relays_select->bind_result($url, $comment, $listeners, $max_listeners, $status, $svers, $peak, $song, $pct);

    $run['relays'] = array();

    $songs = array();

    while ($row = $relays_select->fetch())
    {
      $relay = array("id" => $url, "comment" => $comment, "listeners" => $listeners, "max_listeners" => $max_listeners, "status" => $status, "svers" => $svers, "peak" => $peak, "capacity_pct" => $pct);

      if ($status == 'up') {
        if (isset($songs[$song])) {
          $songs[$song]++;
        } else {
          $songs[$song] = 1;
        }
      }
      array_push($run['relays'], $relay);
    }

    $run['song'] = array_keys($songs, max($songs))[0];

    return $run;
  }
}
?>
