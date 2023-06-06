<?php

/*
    Koda apraksts:
        Kods atbild par pieprasijumu apstradi, darbojoties ar uzdevumu risinajumiem.
        Atkarigi no pieprasitas darbibas, notiek sekojosas operacijas:
        pievienosana, dzesana, redigesana, visu uzdevuma risinajumu atlase un
        specifiska risinajuma datu atlase.
    
    Vlads Muravjovs, 4Ap, Rezeknes Tehnikums, 2023
*/

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
    global $data, $conn, $result, $userdata, $memberdata, $projectdata;

    $assignment = $data->assignment;

    $sql = "SELECT id,name FROM submissions WHERE assignment_id=$assignment";
    $set = $conn->query($sql);

    $submissions = [];
    $result = [];
    while($row = $set->fetch_assoc()){
        $submissions[] = $row;
    }

    $result['submissions'] = $submissions;
    
    //check perms for a_s (add solutions)
    $result['add'] = Perms::ParseUserPerms($projectdata['id'], $userdata['id'], 'a_s');
}

function ViewSpecificSubmission(){
    global $data, $conn, $result, $userdata, $memberdata, $projectdata;

    $submission = $data->id;

    $sql = "SELECT * FROM submissions WHERE id=$submission";
    $result = $conn->query($sql)->fetch_assoc();
    $result['own'] = $result['member_id'] == $memberdata['id'];

    $globalEdit = Perms::ParseUserPerms($projectdata['id'], $userdata['id'], 'e_s');
    if($globalEdit)
        $result['own'] = true;
}

function EditSubmission(){
    global $data, $conn, $result, $userdata, $memberdata;

    $submission = $data->id;
    $editData = json_decode($data->data, true);
    
    if(empty($editData['name']))
        die('error');

    $sql = "UPDATE submissions SET name='". $editData['name'] ."', description='". $editData['description'] ."' WHERE id=$submission";
    $result = $conn->query($sql);
}

function DeleteSubmission(){
    global $data, $conn, $result, $userdata, $memberdata;

    $submissionId = $data->id;
    
    //get submission data
    $sql = "SELECT * FROM submissions WHERE id=$submissionId";
    $submission = $conn->query($sql)->fetch_assoc();

    $link = $submission['id'];
    $linktype = 's';

    //delete comments
    $sql = "DELETE from comments WHERE link=$link AND linktype='$linktype'";
    $conn->query($sql);

    //delete files
    $result = FileUtils::DeleteAllLinkedFiles($data->auth, $link, $linktype);

    //delete the submission
    $sql = "DELETE FROM submissions WHERE id=$submissionId";
    $conn->query($sql);
}

//switch depending on the action
switch($data->action){
    case "add":
        AddSubmission();
        break;
    case "remove":
        break;
    case "edit":
        EditSubmission();
        break;
    case "viewall":
        ViewAllSubmissions();
        break;
    case "view":
        ViewSpecificSubmission();
        break;
    case "delete":
        DeleteSubmission();
        break;
}



echo json_encode($result);