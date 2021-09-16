<?php

session_start();
if(isset($_SESSION["userID"])){
    header('Location:./index.php');
}

$username = $email = $password = $confirmPassword = '';

$errors = array('username'=>'','email'=>'','password'=>'','confirmPassword'=>'');

if(isset($_POST['backtolog'])){
    header("Location:./login.php");
}

if(isset($_POST['create'])){
    require_once('./managers/account-manager.php');
    require_once('./dbconfig.php');

    $conn = DBConfig::getConnection();
    $username = $_POST['username'];
    $email = $_POST['email'];

    $accountManager = new AccountManager($conn);

    $errors['username'] = $accountManager->checkUsername($username);
    $errors['email'] = $accountManager->checkEmail($email);
    $errors['password'] = $accountManager->checkPassword($_POST['password']);
    $errors['confirmPassword'] = $accountManager->confirmPassword($_POST['password'],$_POST['confirmPassword']);


    if($errors['email'] == '' && $errors['username'] == ''){
        $errors['email'] = $accountManager->isTaken("email",$email);
        $errors['username'] = $accountManager->isTaken("username",$username);
    }
    if(!array_filter($errors)){
        $accountManager->createAccount($username,$email,$_POST['password']);
        header('Location:login.php');
    }
}
?>


<!DOCTYPE html>
<html>

<head>
<title>Create an account</title>
<link rel="stylesheet" href="styles.css">
<meta name="viewport" content="width=device-width,initial-scale=1">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
</head>

<body>
<div>
<div class="headerlogin">
    <h1>AskMe?</h1>
</div>
<section class="container text-center bg-light" style="margin-top:70px;  width: fit-content; border: 2px solid #B5B5B5; padding: 50px 100px 20px 100px; border-radius:20px; ">
        <h4 class="title">Create an account</h4>
        <form class="" method="POST" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
            <label for="email">Username:</label></br>
            <input type="text"  class="text-danger1" id="username" name="username" value="<?php echo htmlspecialchars($username)?>"></br>
            <div class="text-danger"><?php echo $errors['username'];?></div>

            <label for="email">E-mail:</label></br>
            <input type="text" class="text-danger1" id="email" name="email" value="<?php echo htmlspecialchars($email)?>"></br>
            <div class="text-danger"><?php echo $errors['email'];?></div>

            <label for="password">Password:</label></br>
            <input type="password" class="text-danger1" id="password" name="password"></br>
            <div class="text-danger"><?php echo $errors['password'];?></div>

            <label for="password">Confirm Pasword:</label></br>
            <input type="password" class="text-danger1" id="confirmPassword" name="confirmPassword"></br>
            <div class="text-danger"><?php echo $errors['confirmPassword'];?></div>

            <div class="center" style="margin:10px; margin-top:40px;">
                <input type="submit" name="create" value="Create" class="button_slide slide_left">
            </div>
            <div>
                <input type="submit" name="backtolog" value="Back to login" class="button_slide slide_left logout">
            </div>
        </form>
    </section>
</div>
</body>
<footer>
    <p>Copyright © 2021</p>
    <p>D.Rojnić, J.Prpić, D.Dražetić</p>
</footer>