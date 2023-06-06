<?php

/*
    Koda apraksts:
        Kods apstrada pieprasijumu izdzest uzdevumu.
        Notiek validacija, tiesibu apstrade un visu saistito datu
        izdzesana.
    
    Vlads Muravjovs, 4Ap, Rezeknes Tehnikums, 2023
*/

include_once('../utils/headers.php');
include_once('../utils/sqlutils.php');
use Utils\SQLFormatter;

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

//see if member has perm e_a (delete assignments)
$allow = Perms::ParseUserPerms($project["id"], $userdata["id"], "d_a");
if(!$allow)
    die("perms");

$formatter = new SQLFormatter();

$link = $data->id;
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
$sql = "DELETE from comments WHERE link=$link AND linktype='$linktype'";
$conn->query($sql);

//delete linked files
FileUtils::DeleteAllLinkedFiles($data->auth, $link, $linktype);

//delete assignments
$sql = "DELETE FROM assignments WHERE id=" . $data->id;
$result = $conn->query($sql);

echo json_encode($result);