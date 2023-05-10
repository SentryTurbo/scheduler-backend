<?php

include_once('../utils/sqlutils.php');
use Utils\SQLFormatter;

include_once('../utils/headers.php');
include_once('../utils/connect.php');
include_once('../utils/encrypt.php');

$formatter = new SQLFormatter();
$sql = "SELECT id,pass FROM users WHERE username='". $_POST['username'] . "'";

$result = $conn->query($sql);

$user = $result->fetch_assoc();
if(is_null($user))
    die('ERROR');

//check password
$loginResult = password_verify($_POST['pass'], $user['pass']);

if($loginResult === true){
    $encryptor = new EncryptorBasic();
    echo json_encode([$_POST['username'], $encryptor->encrypt($_POST['pass'])]);    
}else{
    die("ERROR");
}