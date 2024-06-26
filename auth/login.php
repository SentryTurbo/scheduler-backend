<?php

/*
    Koda apraksts:
        Kods atbild par autorizaciju sistema. Tiek parbauditi
        ievaditie dati un to atbilstiba tiem kuri ir ieksa datu baze.
        Ja autorizacija ir veiksmiga, tad atpakal tiek atsutiti
        autorizacijas dati ar nosifretu paroli, kurus lietotajs
        saglaba uz lokalas sistemas, lai nakotne varetu so lietotaju
        identificet.
    
    Vlads Muravjovs, 4Ap, Rezeknes Tehnikums, 2023
*/

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