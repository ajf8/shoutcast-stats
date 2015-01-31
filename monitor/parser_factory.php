<?php

include_once "shoutcast_parser.php";
include_once "icecast_parser.php";

class ParserFactory
{
  function ParserFactory()
  {
    
  }

  function GetParser($relay, $response)
  {
    if (preg_match("/SHOUTcast Server Version (.*?)<\/a>/", $response, $matches))
    {
      return new ShoutcastParser($relay, $response, "SHOUTcast " . $matches[1]);
    } elseif (preg_match("/(Icecast2) Status/", $response, $matches)) {
      return new IcecastParser($relay, $response, $matches[1]);
    }
  }
}

?>
