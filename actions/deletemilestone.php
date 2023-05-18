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
if(!$allow){
    die("perms");
}

//delete all assignments
//get all assignments
$sql = "SELECT * FROM assignments WHERE milestone_id=". $data->id;
$res = $conn->query($sql);

//delete all linked submissions
while($as = $res->fetch_assoc()){
    $link = $as['id'];
    $linktype = 'a';
    
    //delete submissions
    //get all linked submissions
    $sql = "SELECT * FROM submissions WHERE assignment_id=$link";
    $result = $conn->query($sql);
    
    //loop through submissions and delete linked files and comments
    while($row = $result->fetch_assoc()){
        //delete comments
        $id = $row['id'];
    
        $sql = "DELETE FROM comments WHERE link=$id AND linktype='s'";
        $conn->query($sql);
    
        //delete files
        FileUtils::DeleteAllLinkedFiles($data->auth, $id, 's');
    }
    
    //delete linked submissions
    $sql = "DELETE FROM submissions WHERE assignment_id=$link";
    $conn->query($sql);
    
    //delete linked comments
    $sql = "DELETE from comments WHERE link=$link AND linktype='a'";
    $conn->query($sql);
    
    //delete linked files
    FileUtils::DeleteAllLinkedFiles($data->auth, $link, 'a');
}

//delete assignments
$sql = "DELETE FROM assignments WHERE milestone_id=" . $data->id;
$result = $conn->query($sql);

//delete actual milestone
$sql = "DELETE FROM milestones WHERE id=" . $data->id;
$result = $conn->query($sql);

echo json_encode($result);