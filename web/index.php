<?php
include "model.inc";
$model = new StreamStatsModel();
$stats = $model->GetStats();
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="refresh" content="60" >

    <title>afterhours.fm - relay statistics</title>

    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
    <div class="container">
      <img src="logo.png"/>
      <h2><?php print $stats['listeners']; ?> total listeners</h2>
      <p>Updated at <?php print $stats['dt_created']; ?></p>
      <h3><?php print $stats['song']; ?></h3>
      <table class="table">
        <thead>
          <tr>
            <th>URL</th>
            <th>Comment</th>
            <th>Status</th>
            <th>Version</th>
            <th>Listeners</th>
            <th>Capacity</th>
            <th>Peak</th>
        </thead>
        <tbody>
<?php
foreach($stats["relays"] as $relay)
{
  if ($relay["status"] == "up") {
?>
          <tr>
<?php
} else {
?>
          <tr class="danger">
<?php
}
?>
            <td><a href="<?php print $relay['id']; ?>"><?php print $relay['id']; ?></a></td>
            <td><?php print $relay['comment']; ?></td>
            <td><?php print $relay['status']; ?></td>
            <td><?php print $relay['svers']; ?></td>
            <td><?php print $relay['listeners']; ?></td>
            <td>
<?php
if (isset($relay['capacity_pct'])) {
print $relay["max_listeners"] . " (" . $relay['capacity_pct'] . "%)";
}
?>
</td>
            <td><?php print $relay['peak']; ?></td>
          </tr>
<?php
if (isset($relay['capacity_pct'])) {
?>
<!-- <td colspan="7" style="padding:0px;">
<div class="progress">
<div class="progress-bar" role="progressbar" aria-valuenow="<?php print $relay['capacity_pct']; ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php print $relay['capacity_pct']; ?>%;">
</div>
</div>
</td> -->
<?php
}
}
?>
        </tbody>
      </table>
      <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
      <!-- Include all compiled plugins (below), or include individual files as needed -->
      <script src="js/bootstrap.min.js"></script>
    </div>
  </body>
</html>

