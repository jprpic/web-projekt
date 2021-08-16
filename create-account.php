<?php

$username = $email = $password = $confirmPassword = '';

$errors = array('username'=>'','email'=>'','password'=>'','confirmPassword'=>'');

if(isset($_POST['create'])){
    require_once('./account-validator.php');
    require_once('./dbconfig.php');

    $conn = DBConfig::getConnection();
    $username = $_POST['username'];
    $email = $_POST['email'];

    $accountValidator = new AccountValidator($conn);

    $errors['username'] = $accountValidator->checkUsername($username);
    $errors['email'] = $accountValidator->checkEmail($email);
    $errors['password'] = $accountValidator->checkPassword($_POST['password']);
    $errors['confirmPassword'] = $accountValidator->confirmPassword($_POST['password'],$_POST['confirmPassword']);


    if($errors['email'] == '' && $errors['username'] == ''){
        $errors['email'] = $accountValidator->isTaken("email",$email);
        $errors['username'] = $accountValidator->isTaken("username",$username);
    }
    if(!array_filter($errors)){
        $accountValidator->createAccount($username,$email,$_POST['password']);
        header('Location:login.php');
    }
}


?>


<!DOCTYPE html>
<html>

<head>
<title>Create an account</title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
</head>

<body>
<section class="container text-center bg-light">
        <h4 class="title">Create an account</h4>
        <form class="" method="POST" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
            <label for="email">Username:</label></br>
            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username)?>"></br>
            <div class="text-danger"><?php echo $errors['username'];?></div>

            <label for="email">E-mail:</label></br>
            <input type="text" id="email" name="email" value="<?php echo htmlspecialchars($email)?>"></br>
            <div class="text-danger"><?php echo $errors['email'];?></div>

            <label for="password">Password:</label></br>
            <input type="password" id="password" name="password"></br>
            <div class="text-danger"><?php echo $errors['password'];?></div>

            <label for="password">Confirm Pasword:</label></br>
            <input type="password" id="confirmPassword" name="confirmPassword"></br>
            <div class="text-danger"><?php echo $errors['confirmPassword'];?></div>

            <div class="center" style="margin:10px;">
                <input type="submit" name="create" value="Create" class="btn btn-primary text-white">
            </div>
        </form>
    </section>
</body>
