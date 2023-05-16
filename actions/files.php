<?php

include_once('../utils/headers.php');
include_once('../utils/sqlutils.php');
include_once('../utils/connect.php');
include_once('../utils/user.php');

$result = false;

//get user data
$session = new UserSession();
if(isset($_POST['auth']))
    $userdata = $session->GetUserData($_POST['auth']);

function UploadFile(){
    global $result, $conn;

    //invalid parameters
    if(!isset($_FILES['upfile']['error']) || is_array($_FILES['upfile']['error'])){
        die('params');
    }

    //file size check
    if($_FILES['upfile']['size'] > 1000000){
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

    //attach newly uploaded file to the linked object
    $sql = "INSERT INTO files(url,link,linktype) VALUES ('$fileurl', $link, '$linktype')";
    $conn->query($sql);

    $result = true;
}

function ViewLinkedFiles(){
    global $conn, $result;

    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    $link = $data['link'];
    $linktype = $data['linktype'];

    $sql = "SELECT * FROM files WHERE link=$link AND linktype='$linktype'";
    $query = $conn->query($sql);
    
    $result = [];
    while($row = $query->fetch_assoc()){
        $row['type'] = FileUtils::ParseFiletype($row['url']);
        $result[] = $row;
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