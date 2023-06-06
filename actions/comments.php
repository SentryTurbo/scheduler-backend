<?php

/*
    Koda apraksts:
        Komentaru pieprasijumu apstrades fails. Darbojas adaptivi,
        balstoties uz atsutito "darbibas tipu". Darbibas ir: skatit,
        pievienot un izdzest.
    
    Vlads Muravjovs, 4Ap, Rezeknes Tehnikums, 2023
*/

include_once('../utils/headers.php');
include_once('../utils/sqlutils.php');
include_once('../utils/connect.php');
include_once('../utils/user.php');

$json = file_get_contents('php://input');
$data = json_decode($json);

$result = false;

//get user data
$session = new UserSession();
$userdata = $session->GetUserData($data->auth);

$projectdata = null;
switch($data->linktype){
    case 'a':
        $projectdata = ProjectUtils::GetAssignmentProject($data->link);
        break;
    case 's':
        $sql = "SELECT * FROM submissions WHERE id=".$data->link;
        $sub = $conn->query($sql)->fetch_assoc();
        $projectdata = ProjectUtils::GetAssignmentProject($sub['assignment_id']);
        break;
    }

function ViewComments(){
    global $conn, $result, $data, $userdata, $projectdata;

    $linktype = $data->linktype;
    $link = $data->link;

    //get comments from some sort of post:
    //comment type keys:
    //a - assignment, s - solution
    $sql = "
    SELECT comments.id, comments.content, users.username FROM comments
    INNER JOIN users ON users.id=comments.user_id
    WHERE linktype='$linktype' AND link=$link
    ";
    $comments = $conn->query($sql);

    //dump all comments to a result array
    $comms = [];
    $result = [];

    while($row = $comments->fetch_assoc()){
        $row['own'] = $userdata['username'] == $row['username'];
        $comms[] = $row;
    }

    $result['comments'] = $comms;
    $result['add'] = false;

    if($projectdata === null)
        $result['add'] = true;

    //check if the user has perms to add comments
    if($projectdata !== null){
        //a_c - add comments
        $result['add'] = Perms::ParseUserPerms($projectdata['id'], $userdata['id'], 'a_c');
    }
}

function AddComment(){
    global $conn, $result, $data, $userdata;

    $linktype = $data->linktype;
    $link = $data->link;
    $content = htmlspecialchars($data->content);
    $user = $userdata["id"];

    //create a comment
    if(empty($content) || strlen($content) > 255){
        $result = false;
        die(json_encode($result));
    }

    $sql = "INSERT INTO comments(content,user_id,link,linktype) VALUES ('$content', $user, $link, '$linktype')";
    $result = $conn->query($sql);
}

function DeleteComment(){
    global $conn, $result, $data, $userdata;

    $target = $data->id;
    $linktype = $data->linktype;
    $link = $data->link;

    //delete a comment
    $sql = "DELETE FROM comments WHERE link=$link AND linktype='$linktype' AND id=$target";
    $result = $conn->query($sql);
}

switch($data->action){
    case 'view':
        ViewComments();
        break;
    case 'add':
        AddComment();
        break;
    case 'delete':
        DeleteComment();
        break;
}

echo json_encode($result);