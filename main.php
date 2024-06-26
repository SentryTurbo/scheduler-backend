<?php

/*
    Koda apraksts:
        Atlasa galvena panela datus. Atlasa ar lietotaju
        sasaistitus projektus, merkus un uzdevumus. Izpilda
        nelielus progresa un statistikas aprekinus.
    
    Vlads Muravjovs, 4Ap, Rezeknes Tehnikums, 2023
*/

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

//bubble sort algoritms, lai varetu kartot projektus pec to pabeigsanas statusa
function bubbleSortProjects($arr){
    for($i = 0; $i < count($arr) - 1;++$i){
        for($j = 0; $j < count($arr) - $i - 1; ++$j){
            if($arr[$j]['finish'] && !$arr[$j+1]['finish']){
                $t = $arr[$j];
                $arr[$j] = $arr[$j+1];
                $arr[$j + 1] = $t;
            }
        }
    }

    return $arr;
}

while($row = $result->fetch_assoc()) {
    //calculate project progress
    $sql = "SELECT * FROM milestones WHERE project_id=" . $row['id'];
    $rm = $conn->query($sql);

    //milestone count
    $miles = $rm->num_rows;

    //finished milestone count
    $fmiles = 0;

    while($m = $rm->fetch_assoc()){
        //get assignment count
        $sql = "SELECT * FROM assignments WHERE milestone_id=".$m['id'];
        $ra = $conn->query($sql);

        $acount = $ra->num_rows;

        //get finished assignment count
        $sql = "SELECT * FROM assignments WHERE milestone_id=". $m['id'] ." AND finish_date <> '0000-00-00' AND finish_date IS NOT NULL";
        $ra = $conn->query($sql);

        $fcount = $ra->num_rows;

        if($acount == $fcount)
            $fmiles++;
    }
    
    $row['progress'] = "$fmiles/$miles";

    if($fmiles == $miles)
        $row['finish'] = true;
    else
        $row['finish'] = false;

    //percent
    $row['percent'] = $miles == 0 ? 0 : number_format($fmiles/$miles * 100);

    $projects[] = $row;
}

$projects = bubbleSortProjects($projects);

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
    //calculate milestone progress
    //get number of assignments altogether
    $sql = "SELECT * FROM assignments WHERE milestone_id=".$row['id'];
    $r = $conn->query($sql);

    $acount = $r->num_rows;

    //get number of finished assignments
    $sql = "SELECT * FROM assignments WHERE milestone_id=". $row['id'] ." AND finish_date <> '0000-00-00' AND finish_date IS NOT NULL";
    $r = $conn->query($sql);

    $fcount = $r->num_rows;

    //set progress
    $row['progress'] = "$fcount/$acount";

    //percent
    $row['percent'] = $acount == 0 ? 0 : number_format($fcount/$acount * 100);

    if($fcount == $acount)
        $row['finish'] = true;
    else
        $row['finish'] = false;

    $milestones[] = $row;
}

$response['milestones'] = bubbleSortProjects($milestones);

//get ASSOCIATED assignments
$assignments = array();

$sql = "
    select assignments.id, assignments.name, assignments.description, assignments.milestone_id 
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