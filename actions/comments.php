<?php

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

function ViewComments(){
    global $conn, $result, $data, $userdata;

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
    $result = [];

    while($row = $comments->fetch_assoc()){
        $row['own'] = $userdata['username'] == $row['username'];
        $result[] = $row;
    }
}

function AddComment(){
    global $conn, $result, $data, $userdata;

    $linktype = $data->linktype;
    $link = $data->link;
    $content = $data->content;
    $user = $userdata["id"];

    //create a comment
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