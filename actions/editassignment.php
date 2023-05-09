<?php

include_once('../utils/sqlutils.php');
use Utils\SQLFormatter;

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, X-Requested-With");

$json = file_get_contents('php://input');
$data = json_decode($json);

include_once('../utils/connect.php');

$formatter = new SQLFormatter();

//edit assignment
$sql = "UPDATE assignments SET ". $formatter->set($data->name, $data->value) ." WHERE id=". $data->id;
$result = $conn->query($sql);

echo json_encode($data);