<?php

include_once "relay_finder.php";
include_once "relay.php";

$finder = new RelayFinder($argv[1]);
$relays = $finder->FindRelays();
$db = new mysqli('localhost', 'streamstats', '', 'streamstats') or die('Could not connect to DB: ' . mysql_error());

$run_insert = $db->prepare("INSERT INTO stat_runs () VALUES()");
$run_update = $db->prepare("UPDATE stat_runs SET active=1, listeners=? WHERE id=?");

$relay_insert = $db->prepare("INSERT INTO relay_stats (run_id, url, comment, listeners, max_listeners, status, peak, stream_status, server_status, full_output, http_status, song, svers) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

print $db->error;

$run_insert->execute();
$run_id = $db->insert_id;

$totalListeners = 0;

foreach($relays as $relay)
{
  $stats = $relay->GetStats();
  print "relay: " . $relay->url . " // " . $stats->listeners . " // " . $stats->status . "\n";
  $relay_insert->bind_param('dssddsdsssdss', $run_id, $relay->url, $relay->comment, $stats->listeners, $stats->max_listeners, $stats->status, $stats->peak, $stats->stream_status, $stats->server_status, $stats->full_output, $stats->http_status, $stats->song, $stats->svers);
  $relay_insert->execute();

  if (isset($stats->listeners))
  {
    $totalListeners += $stats->listeners;
  }
}

$run_update->bind_param('dd', $totalListeners, $run_id);
$run_update->execute();

$db->close();

?>
