<?php

include_once('../utils/sqlutils.php');
use Utils\SQLFormatter;

include_once('../utils/headers.php');
include_once('../utils/connect.php');

$formatter = new SQLFormatter();
$sql = "SELECT id,pass FROM users WHERE 'username'='". $_POST['username'] . "'";

$result = $conn->query($sql);

$user = $result->fetch_assoc();

//check password
$loginResult = password_verify($_POST['pass'], $user['pass']);

echo json_encode($loginResult);