<?php

include_once('../utils/sqlutils.php');
use Utils\SQLFormatter;

include_once('../utils/headers.php');
include_once('../utils/connect.php');

$hash = password_hash($_POST['pass'], PASSWORD_DEFAULT);

$formatter = new SQLFormatter();
$sql = "INSERT INTO users (username,pass) VALUES ('". $_POST['username'] ."', '". $hash ."')";

$result = $conn->query($sql);

echo json_encode($result);