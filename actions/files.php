<?php

/*
    Koda apraksts:
        Kods atbild par pieprasijumu apstradi, darbojoties ar failiem.
        Atkarigi no ievaditas "darbibas", notiek atkariga operacija.
        Sis darbibas ir: "augsupieladet", "skatit" un "dzest".
        Faili tiek fiziski izdesti no datora diska tikai tad, ja tie
        vairs nekad netiek atkartoti izmantoti sistema.
    
    Vlads Muravjovs, 4Ap, Rezeknes Tehnikums, 2023
*/

include_once('../utils/headers.php');
include_once('../utils/sqlutils.php');
include_once('../utils/connect.php');
include_once('../utils/user.php');

$result = false;

//get user data
$session = new UserSession();
$userdata = null;
if(isset($_POST['auth']))
    $userdata = $session->GetUserData($_POST['auth']);

function UploadFile(){
    global $result, $conn, $userdata;

    if($userdata === null)
        die('nouser');

    //invalid parameters
    if(!isset($_FILES['upfile']['error']) || is_array($_FILES['upfile']['error'])){
        die('params');
    }

    //file size check
    if($_FILES['upfile']['size'] > 10000000){
        die('filesize');
    }
    
    //file type check
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    if(false === $ext = array_search(
        $finfo->file($_FILES['upfile']['tmp_name']),
        array(
            'jpg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'txt' => 'text/plain',
            'pdf' => 'application/pdf',
            'zip' => 'application/zip',
            'mp3' => 'audio/mpeg',
            'mp4' => 'video/mp4'
        ),
        true
    )) {
        die('format');
    }

    $fileurl = '../files/' . sha1_file($_FILES['upfile']['tmp_name']). '.' .$ext;

    $link = $_POST['link'];
    $linktype = $_POST['linktype'];

    //check if already exists
    $sql = "SELECT * FROM files WHERE url='$fileurl' AND link=$link AND linktype='$linktype'";
    
    if($conn->query($sql)->num_rows > 0)
        die('exists');

    if (!move_uploaded_file(
        $_FILES['upfile']['tmp_name'],
        $fileurl,
    )) {
        die('failed to move file');
    }

    $userid = $userdata['id'];

    //attach newly uploaded file to the linked object
    $sql = "INSERT INTO files(url,link,linktype,user_id) VALUES ('$fileurl', $link, '$linktype', $userid)";
    $conn->query($sql);

    $result = true;
}

function ViewLinkedFiles(){
    global $conn, $result;

    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    $session = new UserSession();
    $userdata = $session->GetUserData($data['auth']);

    $link = $data['link'];
    $linktype = $data['linktype'];

    $projectdata = null;
    switch($linktype){
        case 'a':
            $projectdata = ProjectUtils::GetAssignmentProject($link);
            break;
        case 's':
            $sql = "SELECT * FROM submissions WHERE id=".$link;
            $sub = $conn->query($sql)->fetch_assoc();
            $projectdata = ProjectUtils::GetAssignmentProject($sub['assignment_id']);
            break;
        }

    $sql = "SELECT * FROM files WHERE link=$link AND linktype='$linktype'";
    $query = $conn->query($sql);
    
    $files = [];
    $result = [];
    while($row = $query->fetch_assoc()){
        $row['type'] = FileUtils::ParseFiletype($row['url']);
        $files[] = $row;
    }

    $result['files'] = $files;
    $result['add'] = false;

    //check if the user has perms to add files
    if($projectdata === null){
        $result['add'] = true;
    }

    if($projectdata !== null){
        //a_f - add files
        $result['add'] = Perms::ParseUserPerms($projectdata['id'], $userdata['id'], 'a_f');
    }
}

function DeleteFile(){
    global $conn, $result;

    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    $url = $data['url'];
    $id = $data['id'];

    //check if file gets used numerous times
    $sql = "SELECT * FROM files WHERE url='$url'";
    $initquery = $conn->query($sql);

    //if it isn't, delete it
    if($initquery->num_rows == 1){
        $file = $initquery->fetch_assoc();
        unlink($file['url']);
    }

    //delete the row
    $sql = "DELETE FROM files WHERE id=$id";
    $result = $conn->query($sql);
}

if(isset($_POST['action'])){
    switch($_POST['action']){
        case 'upload':
            UploadFile();
            break;
        case 'view':
            ViewLinkedFiles();
            break;
        case 'delete':
            DeleteFile();
            break;
    }
}else{
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    switch($data['action']){
        case 'upload':
            UploadFile();
            break;
        case 'view':
            ViewLinkedFiles();
            break;
        case 'delete':
            DeleteFile();
            break;
    }
}



echo json_encode($result);