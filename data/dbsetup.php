<?php

/*
    Koda apraksts:
        Sis ir datu bazes instalacijas fails, kurs
        veic vairakus vaicajumus uz datu bazes serveri
        un izveido lietojamo datu bazi caur vienu pogas klikski.
    
    Vlads Muravjovs, 4Ap, Rezeknes Tehnikums, 2023
*/

include_once '../utils/constants.php';

include_once('../utils/creds.php');

// Create connection
$conn = new mysqli($servername, $username, $password);

echo "[DEBUG] Starting setup...";

//confirmation
if(!isset($_POST['confirm'])){
    echo "
        <br>
        <p>Šī operācija izdzēsīs visus iepriekš izveidotos datus. Turpināt?</p>
        <br>
        <form action='/scheduler/data/dbsetup.php' method='post'>
            <input value='Confirm' type='submit' name='confirm'>
        </form>
    ";
    die('');
}

function b(){
    echo '<br>';
}

function d($text){
    b();
    echo '[DEBUG] ' . $text;
}

d('dropping DB');
$sql = "
    DROP DATABASE IF EXISTS scheduler;
";
$result = $conn->query($sql);

d('creating DB');
$sql = "
    CREATE DATABASE scheduler;
";
$result = $conn->query($sql);

d('selecting DB');
$sql = "
    USE scheduler;
";
$result = $conn->query($sql);

d('creating users');
$sql = "
    CREATE TABLE users(
        id INT NOT NULL AUTO_INCREMENT,    
        username VARCHAR(30) NOT NULL,
        pass VARCHAR(255) NOT NULL,
        PRIMARY KEY(id)
    )
";
$result = $conn->query($sql);

d('creating projects');
$sql="
CREATE TABLE projects(
    id INT NOT NULL AUTO_INCREMENT,
    name VARCHAR(20) NOT NULL,
    description TEXT,
    creator INT NOT NULL,
    PRIMARY KEY(id),
    FOREIGN KEY (creator) REFERENCES users(id)
)
";
$result = $conn->query($sql);

d('creating members');
$sql="
CREATE TABLE members(
    id INT NOT NULL AUTO_INCREMENT,
    project_id INT NOT NULL,
    user_id INT NOT NULL,
    perms VARCHAR(255) NOT NULL,
    PRIMARY KEY(id),
    FOREIGN KEY (project_id) REFERENCES projects(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
)
";
$result = $conn->query($sql);

d('creating milestones');
$sql="
CREATE TABLE milestones(
    id INT NOT NULL AUTO_INCREMENT,
    project_id INT NOT NULL,
    name VARCHAR(60) NOT NULL,
    description TEXT,
    PRIMARY KEY(id),
    FOREIGN KEY (project_id) REFERENCES projects(id)
)
";
$result = $conn->query($sql);

d('creating assignments');
$sql="
CREATE TABLE assignments(
    id INT NOT NULL AUTO_INCREMENT,
    milestone_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    finish_date DATE,
    PRIMARY KEY(id),
    FOREIGN KEY (milestone_id) REFERENCES milestones(id)
)
";
$result = $conn->query($sql);

d('creating assignees');
$sql="
CREATE TABLE assignees(
    id INT NOT NULL AUTO_INCREMENT,
    assignment_id INT NOT NULL,
    member_id INT NOT NULL,
    PRIMARY KEY(id),
    FOREIGN KEY (assignment_id) REFERENCES assignments(id),
    FOREIGN KEY (member_id) REFERENCES members(id)
)
";
$result = $conn->query($sql);

d('creating submissions');
$sql="
CREATE TABLE submissions(
    id INT NOT NULL AUTO_INCREMENT,
    assignment_id INT NOT NULL,
    member_id INT NOT NULL,
    name VARCHAR(100),
    description TEXT,
    PRIMARY KEY(id),
    FOREIGN KEY (assignment_id) REFERENCES assignments(id),
    FOREIGN KEY (member_id) REFERENCES members(id)
)
";
$result = $conn->query($sql);

d('creating global tables');
d('creating file table');
$sql="
CREATE TABLE files(
    id INT NOT NULL AUTO_INCREMENT,
    url VARCHAR(255) NOT NULL,
    user_id INT NOT NULL,
    link INT NOT NULL,
    linktype VARCHAR(10) NOT NULL,
    PRIMARY KEY(id)
)
";
$result = $conn->query($sql);

d('creating comment table');
$sql="
CREATE TABLE comments(
    id INT NOT NULL AUTO_INCREMENT,
    content VARCHAR(255) NOT NULL,
    user_id INT NOT NULL,
    link INT NOT NULL,
    linktype VARCHAR(10) NOT NULL,
    PRIMARY KEY(id)
)
";
$result = $conn->query($sql);

d('SUCCESS');