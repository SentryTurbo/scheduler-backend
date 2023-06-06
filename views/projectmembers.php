<?php

/*
    Koda apraksts:
        Atlasa projekta dalibniekus.
    
    Vlads Muravjovs, 4Ap, Rezeknes Tehnikums, 2023
*/

include_once('../utils/headers.php');
include_once('../utils/sqlutils.php');
include_once('../utils/connect.php');
include_once('../utils/user.php');

$json = file_get_contents('php://input');
$data = json_decode($json);

$sql = "
    SELECT users.id, users.username, members.perms 
    FROM members 
    INNER JOIN users ON members.user_id=users.id
    WHERE members.project_id=".$data->project;

$result = $conn->query($sql);

$members = [];
while($row = $result->fetch_assoc()) {
    $members[] = $row;
}

echo json_encode($members);