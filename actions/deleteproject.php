<?php

include_once('../utils/headers.php');
include_once('../utils/sqlutils.php');
use Utils\SQLFormatter;

include_once('../utils/connect.php');

$formatter = new SQLFormatter();

//get milestones that align with the project id
$sql = "SELECT id FROM milestones WHERE project_id=" . $_GET['id'];
$result = $conn->query($sql);

$dataset = $result->fetch_array(MYSQLI_NUM);

if($dataset !== null){
    //delete all linked assignments
    for($i=0;$i<count($dataset);$i++){
        $sql = "DELETE FROM assignments WHERE milestone_id=" . $dataset[$i];
        $result = $conn->query($sql);
    }
}

//delete all milestones
$sql = "DELETE FROM milestones WHERE project_id=" . $_GET['id'];
$result = $conn->query($sql);

//delete the project
$sql = "DELETE FROM projects WHERE id=" . $_GET['id'];
$result = $conn->query($sql);

echo json_encode($result);