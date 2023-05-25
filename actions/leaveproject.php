<?php

include_once('../utils/headers.php');
include_once('../utils/connect.php');
include_once('../utils/user.php');

//get json data
$json = file_get_contents('php://input');
$data = json_decode($json);

//get the current user
$sesh = new UserSession();
$userdata = $sesh->GetUserData($data->auth);

//check if the user is the creator of the project
$project = $data->project;

$sql = "SELECT creator FROM projects WHERE id=$project";
$projectdata = $conn->query($sql)->fetch_assoc();

if($userdata['id'] == $projectdata['creator']){
    die('error');
}

//delete the user from the member list
$sql = "DELETE FROM members WHERE user_id=".$userdata['id']." AND project_id=".$project;
$result = $conn->query($sql);

echo json_encode($result);