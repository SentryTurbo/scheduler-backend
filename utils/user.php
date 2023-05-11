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
        
        if($perms == "all")
            return true;
        
        if(str_contains($perms, $perm))
            return true;

        return false;
    }
}