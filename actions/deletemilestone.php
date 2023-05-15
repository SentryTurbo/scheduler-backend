<?php

include_once('../utils/headers.php');
include_once('../utils/sqlutils.php');
use Utils\SQLFormatter;

include_once('../utils/connect.php');
include_once('../utils/user.php');

//get JSON data
$json = file_get_contents('php://input');
$data = json_decode($json);

$formatter = new SQLFormatter();

//get user session
$session = new UserSession();
$userdata = $session->GetUserData($data->auth);

//see if member has perm d_m (delete milestones)
$allow = Perms::ParseUserPerms($data->project_id, $userdata["id"], "d_m");
if(!$allow)
    die("perms");

//delete linked assignments
$sql = "DELETE FROM assignments WHERE milestone_id=" . $data->id;
$result = $conn->query($sql);

//delete actual milestone
$sql = "DELETE FROM milestones WHERE id=" . $data->id;
$result = $conn->query($sql);

echo json_encode($result);