<?php

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

//see if member has perm d_p (delete project)
$allow = Perms::ParseUserPerms($data->id, $userdata["id"], "d_p");
if(!$allow)
    die("perms");

$formatter = new SQLFormatter();

//get milestones that align with the project id
$sql = "SELECT id FROM milestones WHERE project_id=" . $data->id;
$result = $conn->query($sql);

$dataset = $result->fetch_array(MYSQLI_NUM);

if($dataset !== null){
    //delete all linked assignments
    for($i=0;$i<count($dataset);$i++){
        //get all assignments
        $sql = "SELECT * FROM assignments WHERE milestone_id=". $dataset[$i];
        $res = $conn->query($sql);

        //delete all linked submissions
        while($as = $res->fetch_assoc()){
            $link = $as['id'];
            $linktype = 'a';
            
            //delete submissions
            //get all linked submissions
            $sql = "SELECT * FROM submissions WHERE assignment_id=$link";
            $result = $conn->query($sql);
            
            //loop through submissions and delete linked files and comments
            while($row = $result->fetch_assoc()){
                //delete comments
                $id = $row['id'];
            
                $sql = "DELETE FROM comments WHERE link=$id AND linktype='s'";
                $conn->query($sql);
            
                //delete files
                FileUtils::DeleteAllLinkedFiles($data->auth, $id, 's');
            }
            
            //delete linked submissions
            $sql = "DELETE FROM submissions WHERE assignment_id=$link";
            $conn->query($sql);
            
            //delete linked comments
            $sql = "DELETE from comments WHERE link=$link AND linktype='a'";
            $conn->query($sql);
            
            //delete linked files
            FileUtils::DeleteAllLinkedFiles($data->auth, $link, 'a');
        }

        $sql = "DELETE FROM assignments WHERE milestone_id=" . $dataset[$i];
        $result = $conn->query($sql);
    }
}

//delete all milestones
$sql = "DELETE FROM milestones WHERE project_id=" . $data->id;
$result = $conn->query($sql);

//delete all members
$sql = "DELETE FROM members WHERE project_id=" . $data->id;
$result = $conn->query($sql);

//delete the project
$sql = "DELETE FROM projects WHERE id=" . $data->id;
$result = $conn->query($sql);

echo json_encode($result);