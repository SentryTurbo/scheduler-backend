<?php

header('Access-Control-Allow-Origin: *');

include_once('utils/connect.php');

$sql = "SELECT id,name,description FROM projects";
$result = $conn->query($sql);

$response = array();
$projects = array();

while($row = $result->fetch_assoc()) {
    $projects[] = $row;
}

$response['projects'] = $projects;

//get milestones
$milestones = array();

$sql = "SELECT id,name,description FROM milestones";
$result = $conn->query($sql);

while($row = $result->fetch_assoc()) {
    $milestones[] = $row;
}

$response['milestones'] = $milestones;

//get assignments
$assignments = array();

$sql = "SELECT milestone_id AS id,name,description FROM assignments";
$result = $conn->query($sql);

while($row = $result->fetch_assoc()) {
    $assignments[] = $row;
}

$response['assignments'] = $assignments;

echo json_encode($response);