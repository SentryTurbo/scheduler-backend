<?php

header('Access-Control-Allow-Origin: *');

include_once('utils/headers.php');
include_once('utils/connect.php');
include_once('utils/user.php');

$json = file_get_contents('php://input');

$session = new UserSession();
$userdata = $session->GetUserData($json);

$sql = "
    select projects.id, projects.name, projects.description from members
    inner join projects on members.project_id=projects.id
    where members.user_id=".$userdata['id'];

$result = $conn->query($sql);

$response = array();
$projects = array();

while($row = $result->fetch_assoc()) {
    $projects[] = $row;
}

$response['projects'] = $projects;

//get ASSOCIATED milestones
$milestones = array();

$sql = "
    select milestones.id, milestones.name, milestones.description 
    from members 
    inner join projects on members.project_id=projects.id
    inner join milestones on milestones.project_id=projects.id
    where members.user_id=".$userdata["id"];

$result = $conn->query($sql);

while($row = $result->fetch_assoc()) {
    $milestones[] = $row;
}

$response['milestones'] = $milestones;

//get ASSOCIATED assignments
$assignments = array();

$sql = "
    select assignments.id, assignments.name, assignments.description 
    from members 
    inner join projects on members.project_id=projects.id
    inner join milestones on milestones.project_id=projects.id
    inner join assignments on assignments.milestone_id=milestones.id
    where members.user_id=".$userdata["id"];

$result = $conn->query($sql);

while($row = $result->fetch_assoc()) {
    $assignments[] = $row;
}

$response['assignments'] = $assignments;

echo json_encode($response);