<?php

class RelayStats
{
  var $relay;
  var $status;
  var $http_status;
  var $listeners;
  var $max_listeners;
  var $peak;
  var $song;
  var $server_status;
  var $stream_status;
  var $full_output;

  function RelayStats($relay) {
    $this->relay = $relay;
  }
}

?>
