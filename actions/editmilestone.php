<?php

include_once('../utils/headers.php');
include_once('../utils/user.php');

$json = file_get_contents('php://input');
$data = json_decode($json);

include_once('../utils/sqlutils.php');
use Utils\SQLFormatter;

include_once('../utils/connect.php');

$formatter = new SQLFormatter();

//get user session
$session = new UserSession();
$userdata = $session->GetUserData($data->auth);

//see if member has perm e_m (edit milestones)
$allow = Perms::ParseUserPerms($data->project_id, $userdata["id"], "e_m");
if(!$allow)
    die("perms");

//update milestones
$sql = "SELECT * FROM milestones WHERE id=".$data->id;
if(!empty($data->name) && preg_match("/^[a-zA-Z0-9 ]*$/",$data->name)){
    $sql = "UPDATE milestones SET name='". $data->name . "' WHERE id=". $data->id;
}

$result = $conn->query($sql);

echo json_encode($data);