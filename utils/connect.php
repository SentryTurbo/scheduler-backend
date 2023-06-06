<?php

/*
    Koda apraksts:
        Utilitfails, kurs piesledzas pie datu bazes servera.
    
    Vlads Muravjovs, 4Ap, Rezeknes Tehnikums, 2023
*/

include_once('creds.php');

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("ERROR");
}