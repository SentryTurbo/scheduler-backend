<?php

include_once('utils/headers.php');
include_once('utils/connect.php');
include_once('utils/user.php');

$json = file_get_contents('php://input');

$userSession = new UserSession();

$userData = $userSession->GetUserData($json);
if($userData !== false){
    echo json_encode($userData);
}else{
    echo "error";
}