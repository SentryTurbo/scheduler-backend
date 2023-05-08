<?php

include_once('../utils/sqlutils.php');
use Utils\SQLFormatter;

header('Access-Control-Allow-Origin: *');

include_once('../utils/connect.php');

$dataset = [$_POST['milestone_id'], $_POST['name'], $_POST['description']];

$formatter = new SQLFormatter();
$sql = "INSERT INTO assignments (milestone_id, name,description) VALUES (" . $formatter->formatArray($dataset) . ")";

$result = $conn->query($sql);

echo json_encode($result);