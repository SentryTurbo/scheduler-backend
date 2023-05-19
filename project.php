<?php

header('Access-Control-Allow-Origin: *');

include_once('utils/connect.php');

// Check connection
if ($conn->connect_error) {
  die("ERROR");
}

if(!isset($_GET['id'])){
	echo 'error';
}

$sql = "SELECT id,name,description FROM projects WHERE id=" . $_GET['id'];
$result = $conn->query($sql);

$response = array();
$project = null;

while($row = $result->fetch_assoc()) {
    $project = $row;
}

$response['project'] = $project;

//get milestones
$milestones = array();

$sql = "SELECT id, name, description FROM milestones WHERE project_id = " . $_GET['id'];
$result = $conn->query($sql);

$stats = [
    'milestonecount' => 0,
    'finishedcount' => 0,
];

while($row = $result->fetch_assoc()) {
    $stats['milestonecount']++;

    //add some cool statistics
    //get progress on milestone + % of assignments done
    $mile = $row['id'];

    //get number of assignments altogether
    $sql = "SELECT * FROM assignments WHERE milestone_id=$mile";
    $r = $conn->query($sql);

    $assignmentcount = $r->num_rows;

    //get number of finished assignments
    $sql = "SELECT * FROM assignments WHERE milestone_id=$mile AND finish_date <> '0000-00-00' AND finish_date IS NOT NULL";
    $r = $conn->query($sql);

    $finishcount = $r->num_rows;

    //add this info to the row
    $row['progress'] = "$finishcount/$assignmentcount";

    $percent = 100.00;

    //calculate percentage
    if($assignmentcount > 0){
        $percent = ($finishcount / $assignmentcount) * 100;
        $percent = number_format($percent);

        if($percent > 100)
            $percent = 100;
    }
    
    //add this to the row
    $row['percent'] = $percent;

    //check if its finished
    $row['finish'] = false;
    if($finishcount == $assignmentcount){
        $stats['finishedcount']++;
        $row['finish'] = true;
    }

    if($finishcount == 0)
    {
        $row['percent'] = 0;
        //$row['finish'] = false;
    }

    $milestones[] = $row;
}

//calculate percentage of milestones finished
$stats['percent'] = 0.00;
if($stats['milestonecount'] > 0 && $stats['finishedcount'] > 0){
    $stats['percent'] = ($stats['finishedcount'] / $stats['milestonecount']) * 100;
    $stats['percent'] = number_format($stats['percent']);

    if($stats['percent'] > 100)
        $stats['percent'] = 100;
}

$response['milestones'] = $milestones;
$response['stats'] = $stats;


echo json_encode($response);