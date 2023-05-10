<?php

include_once('../utils/sqlutils.php');
use Utils\SQLFormatter;
include_once('../utils/headers.php');
include_once('../utils/connect.php');

$dataset = [$_POST['project_id'], $_POST['name'], $_POST['description']];

$formatter = new SQLFormatter();
$sql = "INSERT INTO milestones (project_id, name,description) VALUES (" . $formatter->formatArray($dataset) . ")";

$result = $conn->query($sql);

echo json_encode($result);