<?php

include "model.inc";

$model = new StreamStatsModel();
$response = $model->GetStats();

header('Content-Type: application/json');
print json_encode($response);

?>
