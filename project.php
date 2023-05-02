<?php

header('Access-Control-Allow-Origin: *');

include_once('utils/connect.php');

// Check connection
if ($conn->connect_error) {
  die("ERROR");
}

if(!isset($_GET['id'])){
	echo 'error';
}

$sql = "SELECT id,name,description FROM projects WHERE id=" . $_GET['id'];
$result = $conn->query($sql);

$response = array();
$project = null;

while($row = $result->fetch_assoc()) {
    $project = $row;
}

$response['project'] = $project;

//get milestones
$milestones = array();

$sql = "SELECT id, name, description FROM milestones WHERE project_id = " . $_GET['id'];
$result = $conn->query($sql);

while($row = $result->fetch_assoc()) {
    $milestones[] = $row;
}

$response['milestones'] = $milestones;

//get assignments
/*
$assignments = array();

$sql = "SELECT id,name,description FROM assignments WHERE id=" . $_GET['id'];
$result = $conn->query($sql);

while($row = $result->fetch_assoc()) {
    $assignments[] = $row;
}

$response['assignments'] = $assignments;
*/
echo json_encode($response);