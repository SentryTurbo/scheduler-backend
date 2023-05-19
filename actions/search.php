<?php

include_once('../utils/headers.php');
include_once('../utils/sqlutils.php');
include_once('../utils/connect.php');
include_once('../utils/user.php');

$json = file_get_contents('php://input');
$data = json_decode($json);

$session = new UserSession();
$userdata = $session->GetUserData($data->auth);

$matchresults = [];

$querytext = $data->query->text;

//search projects
$sql = "SELECT id,name FROM projects WHERE name LIKE '%$querytext%'";
$r = $conn->query($sql);

while($row = $r->fetch_assoc()){
    $row['type'] = 'project';
    $matchresults[] = $row;
}

//search milestones
$sql = "SELECT id,name,project_id FROM milestones WHERE name LIKE '%$querytext%'";
$r = $conn->query($sql);

while($row = $r->fetch_assoc()){
    $row['type'] = 'milestone';
    $matchresults[] = $row;
}

//search assignments
$sql = "SELECT id,name,milestone_id FROM assignments WHERE name LIKE '%$querytext%'";
$r = $conn->query($sql);

while($row = $r->fetch_assoc()){
    $row['type'] = 'assignment';
    $matchresults[] = $row;
}

//search submissions
$sql = "SELECT id,name,assignment_id FROM submissions WHERE name LIKE '%$querytext%'";
$r = $conn->query($sql);

while($row = $r->fetch_assoc()){
    //fetch the assignment's milestone
    $sql = "SELECT milestone_id FROM assignments WHERE id=".$row['assignment_id'];
    $res = $conn->query($sql)->fetch_assoc();

    $row['milestone'] = $res['milestone_id'];
    $row['type'] = 'submission';
    $matchresults[] = $row;
}

echo json_encode($matchresults);