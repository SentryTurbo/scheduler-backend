<?php

include_once('../utils/headers.php');
include_once('../utils/sqlutils.php');
include_once('../utils/connect.php');
include_once('../utils/user.php');

$json = file_get_contents('php://input');
$data = json_decode($json);

$result = false;

$session = new UserSession();
$userdata = $session->GetUserData($data->auth);

//get the assignment's project data
$projectdata = ProjectUtils::GetAssignmentProject($data->assignment);

//get the project member's data
$memberdata = ProjectUtils::GetUserMember($userdata["id"], $projectdata["id"]);

function AddSubmission(){
    global $data, $conn, $result, $userdata, $memberdata;

    $assignment = $data->assignment;
    $member = $memberdata["id"];
    $submission = $data->name;

    //create the new submission
    $sql = "INSERT INTO submissions(assignment_id, member_id, name) VALUES ($assignment, $member, '$submission')";
    $result = $conn->query($sql);

    //return the new submission in the result
    $lastid = $conn->insert_id;
    
    $sql = "SELECT * FROM submissions WHERE id=$lastid";
    $result = $conn->query($sql)->fetch_assoc();
}

function ViewAllSubmissions(){
    global $data, $conn, $result, $userdata, $memberdata;

    $assignment = $data->assignment;

    $sql = "SELECT id,name FROM submissions WHERE assignment_id=$assignment";
    $set = $conn->query($sql);

    $result = [];
    while($row = $set->fetch_assoc()){
        $result[] = $row;
    }
}

function ViewSpecificSubmission(){
    global $data, $conn, $result, $userdata, $memberdata;

    $submission = $data->id;

    $sql = "SELECT * FROM submissions WHERE id=$submission";
    $result = $conn->query($sql)->fetch_assoc();
}

//switch depending on the action
switch($data->action){
    case "add":
        AddSubmission();
        break;
    case "remove":
        break;
    case "edit":
        break;
    case "viewall":
        ViewAllSubmissions();
        break;
    case "view":
        ViewSpecificSubmission();
        break;
}



echo json_encode($result);