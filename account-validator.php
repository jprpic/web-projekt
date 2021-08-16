<?php

class AccountValidator{
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
        else if(strlen($username)<4){
            return "Username must be at least 4 characters long!";
        }
        else if(strlen($username)>32){
            return "Username is too long!";
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
            $sanitizedEmail = filter_var($this->email, FILTER_SANITIZE_EMAIL);
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

}

?>
