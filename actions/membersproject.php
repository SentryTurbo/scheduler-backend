<?php

include_once('../utils/headers.php');
include_once('../utils/sqlutils.php');
include_once('../utils/connect.php');
include_once('../utils/user.php');

$json = file_get_contents('php://input');
$data = json_decode($json);

$result = false;

//queries
function addMember(){
    global $data;
    global $conn;
    global $result;

    //search for the member by name
    $sql = "SELECT id FROM users WHERE username='".$data->user . "'";
    $result = $conn->query($sql);
    $member = $result->fetch_assoc();

    //check if member exists
    if(!is_null($member)){
        //check if member is already part of the project
        $sql = "SELECT id FROM members WHERE user_id='". $member["id"] ."' AND project_id='". $data->project ."'";
        $result = $conn->query($sql);
        $projMember = $result->fetch_assoc();
        
        //if member is not part of project already, finally add them
        if(is_null($projMember)){
            $values = "'". $member["id"] . "','" . $data->project . "','" . $data->perms . "'";

            $sql = "INSERT INTO members(user_id,project_id,perms) VALUES ($values)";
            $result = $conn->query($sql);
        }
    }
}

function removeMember(){
    global $data;
    global $conn;
    global $result;

    $session = new UserSession();
    $userdata = $session->GetUserData($data->auth);

    if($userdata['id'] != $data->user){
        $sql = "DELETE FROM members WHERE project_id=".$data->project." AND user_id=".$data->user;
        $result = $conn->query($sql);
    }
}

//switch depending on the action
switch($data->action){
    case "add":
        addMember();
        break;
    case "remove":
        removeMember();
        break;
    case "edit":
        break;
}



echo json_encode($result);