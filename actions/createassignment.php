<?php

/*
    Koda apraksts:
        Kods apstrada pieprasijumu izveidot jaunu uzdevumu.
        Notiek validacija, lietotaja tiesibu parbaude un
        vaicajums uz datu bazes serveri.
    
    Vlads Muravjovs, 4Ap, Rezeknes Tehnikums, 2023
*/

include_once('../utils/sqlutils.php');
use Utils\SQLFormatter;
include_once('../utils/headers.php');
include_once('../utils/connect.php');
include_once('../utils/user.php');

if(!preg_match("/^[a-zA-Z0-9 ]*$/",$_POST['name']) || (!empty($_POST['description']) && !preg_match("/^[a-zA-Z0-9 ]*$/",$_POST['description'])))
    die("error");

//get user session
$session = new UserSession();
$userdata = $session->GetUserData($_POST['auth']);

//get project id
$project = ProjectUtils::GetMilestoneProject($_POST['milestone_id']);

//see if member has perm a_a (add assignments)
$allow = Perms::ParseUserPerms($project["id"], $userdata["id"], "a_a");
if(!$allow)
    die("perms");

$dataset = [$_POST['milestone_id'], $_POST['name'], $_POST['description']];

$formatter = new SQLFormatter();
$sql = "INSERT INTO assignments (milestone_id, name,description) VALUES (" . $formatter->formatArray($dataset) . ")";

$result = $conn->query($sql);

echo json_encode($result);