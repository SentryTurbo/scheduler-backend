<?php

include_once('../utils/headers.php');
include_once('../utils/sqlutils.php');
use Utils\SQLFormatter;

include_once('../utils/connect.php');
include_once('../utils/user.php');

$json = file_get_contents('php://input');
$data = json_decode($json);

//get user session
$session = new UserSession();
$userdata = $session->GetUserData($data->auth);

//see if member has perm d_p (delete project)
$allow = Perms::ParseUserPerms($data->id, $userdata["id"], "d_p");
if(!$allow)
    die("perms");

$formatter = new SQLFormatter();

//get milestones that align with the project id
$sql = "SELECT id FROM milestones WHERE project_id=" . $data->id;
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
$sql = "DELETE FROM milestones WHERE project_id=" . $data->id;
$result = $conn->query($sql);

//delete all members
$sql = "DELETE FROM members WHERE project_id=" . $data->id;
$result = $conn->query($sql);

//delete the project
$sql = "DELETE FROM projects WHERE id=" . $data->id;
$result = $conn->query($sql);

echo json_encode($result);