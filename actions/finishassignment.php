<?php

/*
    Koda apraksts:
        Kods parstrada pieprasijumu atzimet uzdevumu ka pabeigtu.
        Sis notiek tikai tad, ja lietotajam ir tiesibas rediget
        uzdevumus.
    
    Vlads Muravjovs, 4Ap, Rezeknes Tehnikums, 2023
*/

include_once('../utils/sqlutils.php');
use Utils\SQLFormatter;

include_once('../utils/headers.php');
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

//see if member has perm e_a (edit assignments)
$allow = Perms::ParseUserPerms($project["id"], $userdata["id"], "a_a");
if(!$allow)
    die("perms");

$formatter = new SQLFormatter();

//edit assignment
$finish = $data->set == 'finish' ? date('Y-m-d H:i:s') : '0000-00-00';

$sql = "UPDATE assignments SET finish_date='". $finish ."' WHERE id=". $data->id;
$result = $conn->query($sql);

echo json_encode($data);