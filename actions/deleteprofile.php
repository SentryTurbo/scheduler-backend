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

//check if the user has created any projects
$sql = "SELECT * FROM projects WHERE creator=".$userdata['id'];

$res = $conn->query($sql);
if($res->num_rows > 0)
    die('error');

//delete the user from all projects
$sql = "DELETE FROM members WHERE user_id=".$userdata['id'];
$res = $conn->query($sql);

//delete all user files
FileUtils::DeleteAllUserFiles($userdata['id']);

//delete all user comments
$sql = "DELETE FROM comments WHERE user_id=".$userdata['id'];
$res = $conn->query($sql);

//delete the user
$sql = "DELETE FROM users WHERE id=".$userdata['id'];
$res = $conn->query($sql);

echo json_encode($res);