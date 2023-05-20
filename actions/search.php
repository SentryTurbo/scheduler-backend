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
if($data->query->filters->project){
    $sql = "
    select projects.id, projects.name, projects.description from members
    inner join projects on members.project_id=projects.id
    where members.user_id=".$userdata['id']. " and name LIKE '%$querytext%'";

    //$sql = "SELECT id,name FROM projects WHERE name LIKE '%$querytext%'";
    $r = $conn->query($sql);

    while($row = $r->fetch_assoc()){
        $row['type'] = 'project';
        $matchresults[] = $row;
    }
}

//search milestones
if($data->query->filters->milestone){
    $sql = "
    select milestones.id, milestones.name, milestones.description 
    from members 
    inner join projects on members.project_id=projects.id
    inner join milestones on milestones.project_id=projects.id
    where members.user_id=".$userdata['id']. " and milestones.name LIKE '%$querytext%'";

    //$sql = "SELECT id,name,project_id FROM milestones WHERE name LIKE '%$querytext%'";
    $r = $conn->query($sql);

    while($row = $r->fetch_assoc()){
    $row['type'] = 'milestone';
    $matchresults[] = $row;
    }
}


//search assignments
if($data->query->filters->assignment){
    $sql = "
        select assignments.id, assignments.name, assignments.description, assignments.milestone_id 
        from members 
        inner join projects on members.project_id=projects.id
        inner join milestones on milestones.project_id=projects.id
        inner join assignments on assignments.milestone_id=milestones.id
        where members.user_id=".$userdata["id"]." and assignments.name LIKE '%$querytext%'";

    //$sql = "SELECT id,name,milestone_id FROM assignments WHERE name LIKE '%$querytext%'";
    $r = $conn->query($sql);

    while($row = $r->fetch_assoc()){
        $row['type'] = 'assignment';
        $matchresults[] = $row;
    }
}


//search submissions
if($data->query->filters->submission){
    $sql = "
        select submissions.id, submissions.name, submissions.description, submissions.assignment_id 
        from members 
        inner join projects on members.project_id=projects.id
        inner join milestones on milestones.project_id=projects.id
        inner join assignments on assignments.milestone_id=milestones.id
        inner join submissions on submissions.assignment_id=assignments.id
        where members.user_id=".$userdata["id"]." and submissions.name LIKE '%$querytext%'";

    //$sql = "SELECT id,name,assignment_id FROM submissions WHERE name LIKE '%$querytext%'";
    $r = $conn->query($sql);

    while($row = $r->fetch_assoc()){
        //fetch the assignment's milestone
        $sql = "SELECT milestone_id FROM assignments WHERE id=".$row['assignment_id'];
        $res = $conn->query($sql)->fetch_assoc();

        $row['milestone'] = $res['milestone_id'];
        $row['type'] = 'submission';
        $matchresults[] = $row;
    }
}

echo json_encode($matchresults);