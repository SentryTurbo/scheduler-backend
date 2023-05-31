<?php

include_once('../utils/sqlutils.php');
use Utils\SQLFormatter;

include_once('../utils/headers.php');
include_once('../utils/connect.php');

$pass = str_replace(['"',"'"], "", $_POST['pass']);

if(strlen($pass) < 8){
    die(false);
}

$hash = password_hash($pass, PASSWORD_DEFAULT);

//check if user exists
$sql = "SELECT username FROM users WHERE username='".  $_POST['username'] . "'";
$result = $conn->query($sql);

if($result->num_rows > 0)
    die('userexists');

$formatter = new SQLFormatter();
$sql = "INSERT INTO users (username,pass) VALUES ('". $_POST['username'] ."', '". $hash ."')";

$result = $conn->query($sql);

echo json_encode($result);