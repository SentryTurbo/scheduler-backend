<?php

/*
    Koda apraksts:
        Kods apstrada pieprasijumu izdzest projektu.
        Notiek validacija, tiesibu apstrade un visu saistito datu
        izdzesana.
    
    Vlads Muravjovs, 4Ap, Rezeknes Tehnikums, 2023
*/

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
$sql = "SELECT * FROM milestones WHERE project_id=" . $data->id;
$result = $conn->query($sql);

while($milestone = $result->fetch_assoc()){
    
    //get all assignments
    $ar = $conn->query("SELECT * FROM assignments WHERE milestone_id=".$milestone['id']);
    while($assignment = $ar->fetch_assoc()){
        //get all submissions
        $sr = $conn->query("SELECT * FROM submissions WHERE assignment_id=".$assignment['id']);
        
        while($submission = $sr->fetch_assoc()){
            //delete comments
            $conn->query("DELETE from comments WHERE link=". $submission['id'] ." AND linktype='s'");

            //delete files
            FileUtils::DeleteAllLinkedFiles($data->auth, $submission['id'], 's');
        }

        //delete submissions
        $sr = $conn->query("DELETE FROM submissions WHERE assignment_id=".$assignment['id']);

        //delete comments
        $conn->query("DELETE from comments WHERE link=". $assignment['id'] ." AND linktype='a'");

        //delete files
        FileUtils::DeleteAllLinkedFiles($data->auth, $assignment['id'], 'a');
    }

    //delete all assignments
    $conn->query("DELETE FROM assignments WHERE milestone_id=".$milestone['id']);
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