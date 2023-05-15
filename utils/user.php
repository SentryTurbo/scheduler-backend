<?php

include_once('connect.php');
include_once('encrypt.php');

class UserSession{
    public function GetUserData($auth){
        global $conn;

        if($this->AttemptAuth($auth)){
            $authdata = $this->FetchAuthData($auth);

            //fetch accessory data from the database
            $sql = "SELECT id,username FROM users WHERE username='" . $authdata[0] . "'";
            $result = $conn->query($sql);

            return $result->fetch_assoc();
        }else{
            return false;
        }
    }

    public function AttemptAuth($auth){
        global $conn;

        $authdata = $this->FetchAuthData($auth);

        //check if auth is good
        $sql = "SELECT id,pass FROM users WHERE username='". $authdata[0] . "'";

        $result = $conn->query($sql);

        //fetch row
        $user = $result->fetch_assoc();
        if(is_null($user)) //if user doesnt exist, error
            return false;

        //check password
        $loginResult = password_verify($authdata[1], $user['pass']);

        if($loginResult === true){ //if password incorrect, error
            return true;  
        }else{
            return false;
        }
    }

    function FetchAuthData($auth){
        $authdata = json_decode($auth);
        $encryptor = new EncryptorBasic();
        
        //decrypt the password
        $authdata[1] = $encryptor->decrypt($authdata[1]);

        return $authdata;
    }
}

class Perms{
    public static function ParseUserPerms($project, $user, $perm){
        global $conn;

        $sql = "SELECT perms FROM members WHERE members.project_id=$project AND members.user_id=$user";
        $result = $conn->query($sql);

        $perms = $result->fetch_assoc();
        
        $allperms = explode(",", $perms["perms"]);
        foreach ($allperms as $value) {
            if($value == "all")
                return true;

            if($value == $perm){
                return true;
            }
            if($value === $perm){
                return true;
            }
        }

        return false;
    }
}

class ProjectUtils{
    public static function GetAssignmentProject($assignmentid){
        global $conn;

        //get milestone id
        $sql = "SELECT milestone_id FROM assignments WHERE id=$assignmentid";
        $result = $conn->query($sql);
        $milestone = $result->fetch_assoc()["milestone_id"];

        //get project id
        $sql = "SELECT project_id FROM milestones WHERE id=$milestone";
        $result = $conn->query($sql);
        $project = $result->fetch_assoc()["project_id"];

        //get project data
        $sql = "SELECT * FROM projects WHERE id=$project";
        $result = $conn->query($sql);

        return $result->fetch_assoc();
    }

    public static function GetMilestoneProject($milestoneid){
        global $conn;
        
        //get project id
        $sql = "SELECT project_id FROM milestones WHERE id=$milestoneid";
        $result = $conn->query($sql);
        $project = $result->fetch_assoc()["project_id"];

        //get project data
        $sql = "SELECT * FROM projects WHERE id=$project";
        $result = $conn->query($sql);

        return $result->fetch_assoc();
    }

    public static function GetUserMember($user, $project){
        global $conn;

        $sql = "SELECT * FROM members WHERE project_id=$project AND user_id=$user";
        $result = $conn->query($sql);

        return $result->fetch_assoc();
    }
}