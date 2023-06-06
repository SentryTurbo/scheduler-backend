<?php

/*
    Koda apraksts:
        Kods apstrada pieprasijumu izveidot jaunu merki.
        Notiek validacija, lietotaja tiesibu parbaude un
        vaicajums uz datu bazes serveri.
    
    Vlads Muravjovs, 4Ap, Rezeknes Tehnikums, 2023
*/

include_once('../utils/sqlutils.php');
use Utils\SQLFormatter;
include_once('../utils/headers.php');
include_once('../utils/connect.php');
include_once('../utils/user.php');

$dataset = [$_POST['project_id'], $_POST['name'], '' /*$_POST['description']*/];

//get user session
$session = new UserSession();
$userdata = $session->GetUserData($_POST['auth']);

if(!preg_match("/^[a-zA-Z0-9 ]*$/",$_POST['name']))
    die("error");

//see if member has perm a_mb (add members)
$allow = Perms::ParseUserPerms($_POST['project_id'], $userdata["id"], "a_m");
if(!$allow)
    die("perms");

$formatter = new SQLFormatter();
$sql = "INSERT INTO milestones (project_id, name,description) VALUES (" . $formatter->formatArray($dataset) . ")";

$result = $conn->query($sql);

echo json_encode($result);