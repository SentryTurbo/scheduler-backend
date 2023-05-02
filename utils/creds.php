<?php

include_once 'constants.php';

use function Utils\getConst;

$servername = getConst('db.address');
$username = getConst('db.user');
$password = getConst('db.pass');
$dbname = getConst('db.dbname');