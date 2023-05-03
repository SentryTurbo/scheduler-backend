<?php
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, X-Requested-With");

include_once('../utils/sqlutils.php');
use Utils\SQLFormatter;

include_once('../utils/connect.php');

$formatter = new SQLFormatter();

//delete linked assignments
$sql = "DELETE FROM assignments WHERE milestone_id=" . $_GET['id'];
$result = $conn->query($sql);

//delete actual milestone
$sql = "DELETE FROM milestones WHERE id=" . $_GET['id'];
$result = $conn->query($sql);

echo json_encode($result);