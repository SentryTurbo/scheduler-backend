<?php

/*
    Koda apraksts:
        Atlasa merka panela datus. Atlasa pabeigtus un nepabeigtus
        merka uzdevumus.
    
    Vlads Muravjovs, 4Ap, Rezeknes Tehnikums, 2023
*/

header('Access-Control-Allow-Origin: *');

include_once('utils/connect.php');

$sql = "SELECT id,name,description,project_id FROM milestones WHERE id=" . $_GET['id'];
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
$sql = "SELECT id,name,description,finish_date FROM assignments WHERE milestone_id=" . $_GET['id'] . " AND (finish_date IS NULL OR finish_date='0000-00-00')";
$result = $conn->query($sql);

while($row = $result->fetch_assoc()) {
    $unfinishedassignments[] = $row;
}

//get finished ones
$sql = "SELECT id,name,description,finish_date FROM assignments WHERE milestone_id=" . $_GET['id'] . " AND finish_date IS NOT NULL AND finish_date <> '0000-00-00'";
$result = $conn->query($sql);

while($row = $result->fetch_assoc()) {
    $finishedassignments[] = $row;
}

$response['unfinishedassignments'] = $unfinishedassignments;
$response['finishedassignments'] = $finishedassignments;

echo json_encode($response);