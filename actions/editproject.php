<?php

include_once('../utils/headers.php');

$json = file_get_contents('php://input');
$data = json_decode($json);

include_once('../utils/sqlutils.php');
use Utils\SQLFormatter;

include_once('../utils/connect.php');

$formatter = new SQLFormatter();

//delete linked assignments
$sql = "UPDATE projects SET name='". $data->name . "' WHERE id=". $data->id;
$result = $conn->query($sql);

echo json_encode($data);