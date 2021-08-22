<?php

class AccountManager{
    public $conn;

    public function __construct($conn){
        $this->conn = $conn;
    }

    public function __destruct()
    {
        $this->conn = null;
    }

    public function checkUsername($username){
        if(empty($username)){
            return "A username is required!";
        }
        else if(strlen($username)<3){
            return "Username must be at least 4 characters long!";
        }
        else if(strlen($username)>32){
            return "Username is too long!";
        }
        else if(!preg_match('/^[a-z\d_]{2,32}$/i', $username)) {
            return "Username must contain characters, digits or underscore only!";
        }
        else{
            return "";
        }
    }

    public function checkEmail($email){
        if(empty($email)){
            return 'An email address is required!';
        }
        else{
            $sanitizedEmail = filter_var($email, FILTER_SANITIZE_EMAIL);
            if (!($email == $sanitizedEmail && filter_var($email, FILTER_VALIDATE_EMAIL))) {
                return "Email address is not valid!";
            }
            else{
                return "";
            }
        }
    }


    public function checkPassword($password){
        if(empty($password)){
            return 'A password is required!';
        }
        else if(strlen($password)<3){
            return 'Password is too short!';
        }
        else if (strlen($password)>32){
            return 'Password is too long!';
        }
        else{
            return "";
        }
    }

    public function confirmPassword($password,$confirmPassword){
        if(empty($confirmPassword)){
            return "Please confirm your password!";
        }
        else if($password != $confirmPassword){
            return "Passwords don't match!";
        }
    }


    public function isTaken($attribute,$value){
        $query = $this->conn->prepare("SELECT * FROM USERS WHERE $attribute = :$attribute");

        try {
            $query->execute([":$attribute" => $value]);
            $query->setFetchMode(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            echo $e->getMessage();
        }

        $user = $query->fetch();
        if($user){
            return "This $attribute is already taken!";
        }
        else{
            return "";
        }
    }

    public function createAccount($username,$email,$password){
        $user = array(
            ':email' => $email,
            ':username' => $username,
            ':password' => hash("sha256", $password)
        );

        $sql = <<<EOSQL
            INSERT INTO Users (email, username, password) VALUES(:email, :username, :password);
        EOSQL;

        $stmt= $this->conn->prepare($sql);
        $stmt->execute($user);
    }

    public function getAccount($attribute,$value,$password){
        $query = $this->conn->prepare("SELECT * FROM USERS WHERE $attribute = :$attribute");
        $query->execute([":$attribute" => $value]);
        $query->setFetchMode(PDO::FETCH_ASSOC);

        $user = $query->fetch();
        if($user['password']==hash("sha256", $password)){
            return $user;
        }
        else{
            return '';
        }
    }

    public function getUsername($userID){
        $username = $this->conn->prepare("SELECT username FROM Users WHERE id = :userID");
        $username->execute([':userID'=>$userID]);
        $username = $username->fetch(PDO::FETCH_ASSOC);
        return $username['username'];
    }

    public function isAdmin($userID){
        $isAdmin = $this->conn->prepare("SELECT * FROM Admins WHERE userID = :userID");
        $isAdmin->execute([':userID'=>$userID]);
        $isAdmin = $isAdmin->fetch(PDO::FETCH_ASSOC);
        return (bool)$isAdmin;
    }
}

?>
