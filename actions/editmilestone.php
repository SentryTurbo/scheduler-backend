<?php

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, X-Requested-With");

$json = file_get_contents('php://input');
$data = json_decode($json);

include_once('../utils/sqlutils.php');
use Utils\SQLFormatter;

include_once('../utils/connect.php');

$formatter = new SQLFormatter();

//delete linked assignments
$sql = "UPDATE milestones SET name='". $data->name . "' WHERE id=". $data->id;
$result = $conn->query($sql);

echo json_encode($data);