<?php

header('Access-Control-Allow-Origin: *');

include_once('utils/connect.php');

if(!isset($_GET['id'])){
	die("ERROR");
}

$sql = "SELECT id,name,description FROM milestones WHERE id=" . $_GET['id'];
$result = $conn->query($sql);

$response = array();
$milestone = null;

while($row = $result->fetch_assoc()) {
    $milestone = $row;
}

$response['milestone'] = $milestone;

//get assignments

$unfinishedassignments = array();
$finishedassignments = array();

//get unfinished ones
$sql = "SELECT id,name,description FROM assignments WHERE milestone_id=" . $_GET['id'] . " AND finish_date IS NULL";
$result = $conn->query($sql);

while($row = $result->fetch_assoc()) {
    $unfinishedassignments[] = $row;
}

//get finished ones
$sql = "SELECT id,name,description FROM assignments WHERE milestone_id=" . $_GET['id'] . " AND finish_date IS NOT NULL";
$result = $conn->query($sql);

while($row = $result->fetch_assoc()) {
    $finishedassignments[] = $row;
}

$response['unfinishedassignments'] = $unfinishedassignments;
$response['finishedassignments'] = $finishedassignments;

echo json_encode($response);