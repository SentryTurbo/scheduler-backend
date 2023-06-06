<?php

/*
    Koda apraksts:
        Utilitfails, kurs ielade datu bazes
        autorizacijas datus caur utilitfunkciju 'getConst' (skat. constants.php)
    
    Vlads Muravjovs, 4Ap, Rezeknes Tehnikums, 2023
*/

include_once 'constants.php';

use function Utils\getConst;

$servername = getConst('db.address');
$username = getConst('db.user');
$password = getConst('db.pass');
$dbname = getConst('db.dbname');