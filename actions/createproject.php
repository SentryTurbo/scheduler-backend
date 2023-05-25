<?php

include_once('../utils/sqlutils.php');
use Utils\SQLFormatter;
include_once('../utils/headers.php');
include_once('../utils/connect.php');
include_once('../utils/user.php');

$dataset = [$_POST['name'], $_POST['description']];

//get the current user
$sesh = new UserSession();
$userdata = $sesh->GetUserData($_POST['auth']);

//create the project
$sql = "INSERT INTO projects (name,description,creator) VALUES ('" . $_POST['name'] . "', '". $_POST['description'] ."',". $userdata['id'] .")";

$result = $conn->query($sql);

$projectId = $conn->insert_id;

echo $projectId;

//assign user to the project
$sql = "INSERT INTO members (project_id, user_id, perms) VALUES ($projectId, ". $userdata['id'] .", 'all')";
$result = $conn->query($sql);

echo json_encode($result);