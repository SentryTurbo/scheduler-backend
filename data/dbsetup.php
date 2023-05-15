<?php

include_once '../utils/constants.php';
include_once '../utils/creds.php';
include_once '../utils/connect.php';

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
    PRIMARY KEY(id)
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
    finish_date DATE,
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

d('SUCCESS');