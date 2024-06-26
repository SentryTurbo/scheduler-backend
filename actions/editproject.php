<?php

/*
    Koda apraksts:
        Kods apstrada pieprasijumu rediget projektu.
        Notiek validacija, tiesibu apstrade un datu atjaunosana.
    
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

//see if member has perm e_p (edit project)
$allow = Perms::ParseUserPerms($data->id, $userdata["id"], "e_p");
if(!$allow)
    die("perms");

$formatter = new SQLFormatter();

$sql = "SELECT * FROM projects WHERE id=".$data->id;
if(!empty($data->name) && preg_match("/^[a-zA-Z0-9 ]*$/",$data->name)){
    $sql = "UPDATE projects SET name='". $data->name . "' WHERE id=". $data->id;
}

$result = $conn->query($sql);

echo json_encode($data);