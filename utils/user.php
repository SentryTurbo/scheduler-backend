<?php

/*
    Koda apraksts:
        Fails satur sevi utilitklases: 
        
        'UserSession' atbild par lietotaja identificesanu;

        'Perms' atbild par lietotaja tiesibu apstradi;

        'ProjectUtils' atbild par utilitfunkcijam, kuras palidz atrast specifisku datu objektu saistitos datus,
        piemeram, atrast noteikta uzdevuma projekta ID;

        'FileUtils' atbild par failiem, satur sevi izdesanas funkcionalitati un
        ari faila tipa apstradi.
    
    Vlads Muravjovs, 4Ap, Rezeknes Tehnikums, 2023
*/

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

        //check if user is creator of project, if so, user has all perms automatically
        $sql = "SELECT creator FROM projects WHERE id=$project";
        $result = $conn->query($sql)->fetch_assoc();
        if($result['creator'] == $user)
            return true;

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

class FileUtils{
    public static function ParseFiletype($url){
        if(str_ends_with($url, '.png') || str_ends_with($url, '.jpg') || str_ends_with($url, '.gif'))
            return 'Image';
        
        if(str_ends_with($url, '.exe'))
            return 'Executeable'; 

        if(str_ends_with($url, '.zip') || str_ends_with($url, '.rar'))
            return 'Compressed folder';
        
        return 'Unknown';
    }

    public static function DeleteAllLinkedFiles($auth, $link, $linktype){
        global $conn;

        $result = true;

        //get all the files we need
        $sql = "SELECT * FROM files WHERE link=$link AND linktype='$linktype'";
        $allfiles = $conn->query($sql);

        while($row = $allfiles->fetch_assoc()){
            $url = $row['url'];
            $id = $row['id'];
            
            //check if file gets used numerous times
            $sql = "SELECT * FROM files WHERE url='$url'";
            $initquery = $conn->query($sql);

            //if it isn't, delete it
            if($initquery->num_rows == 1){
                $file = $initquery->fetch_assoc();
                unlink($file['url']);
            }

            //delete the row
            $sql = "DELETE FROM files WHERE id=$id";
            $result = $conn->query($sql);
        }

        return $result;
    }

    public static function DeleteAllUserFiles($user){
        global $conn;

        $result = true;

        //get all the files we need
        $sql = "SELECT * FROM files WHERE user_id=$user";
        $allfiles = $conn->query($sql);

        while($row = $allfiles->fetch_assoc()){
            $url = $row['url'];
            $id = $row['id'];
            
            //check if file gets used numerous times
            $sql = "SELECT * FROM files WHERE url='$url'";
            $initquery = $conn->query($sql);

            //if it isn't, delete it
            if($initquery->num_rows == 1){
                $file = $initquery->fetch_assoc();
                unlink($file['url']);
            }

            //delete the row
            $sql = "DELETE FROM files WHERE id=$id";
            $result = $conn->query($sql);
        }

        return $result;
    }
}