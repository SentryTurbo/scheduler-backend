<?php

include_once('../utils/sqlutils.php');
use Utils\SQLFormatter;

include_once('../utils/headers.php');
include_once('../utils/connect.php');
include_once('../utils/user.php');

//get json data
$json = file_get_contents('php://input');
$data = json_decode($json);

//get user session
$session = new UserSession();
$userdata = $session->GetUserData($data->auth);

//get project id
$project = ProjectUtils::GetAssignmentProject($data->id);

//see if member has perm e_a (edit assignments)
$allow = Perms::ParseUserPerms($project["id"], $userdata["id"], "a_a");
if(!$allow)
    die("perms");

$formatter = new SQLFormatter();

$name = $data->name;
$description = $data->description;

//edit assignment
$sql = "SELECT * FROM assignments WHERE id=".$data->id;
if(!empty($data->name) && preg_match("/^[a-zA-Z0-9 ]*$/",$data->name)){
    $sql = "UPDATE assignments SET name='$name', description='$description' WHERE id=". $data->id;
}

$result = $conn->query($sql);

echo json_encode($data);