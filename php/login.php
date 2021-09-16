<?php

session_start();
if(isset($_SESSION["userID"])){
    header('Location:./index.php');
}

$email = $password = "";
$errors = array('name'=>'','password'=>'');

if(isset($_POST['create'])){
    header("Location:./create-account.php");
}
if(isset($_POST['submit'])){
    require_once('./managers/account-manager.php');
    require_once('./dbconfig.php');
    $accountManager = new AccountManager(DBConfig::getConnection());

    $username = $email = $_POST['email'];
    $password = $_POST['password'];
    $user = '';

    if(!$accountManager->checkEmail($email) && $accountManager->isTaken("email",$email)){
        $user = $accountManager->getAccount("email",$email,$password);
        if(!$user){
            $errors['password'] = "Incorrect password!";
        }
    }
    else if(!$accountManager->checkUsername($username) && $accountManager->isTaken("username",$username)){
        $user = $accountManager->getAccount("username",$username,$password);
        if(!$user){
            $errors['password'] = "Incorrect password!";
        }
    }
    else{
        $errors['name'] = "Sorry, user with these credentials can't be found.";
    }


    if($user){
        session_start();
        $_SESSION["userID"]=$user['id'];
        if($accountManager->isAdmin($user['id'])){
            $_SESSION["admin"]='true';
        }
        header('Location:index.php');
    }
}
?>


<!DOCTYPE html>
<html>

<head>
<title>Log in</title>
<link rel="stylesheet" href="styles.css">
<meta name="viewport" content="width=device-width,initial-scale=1">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
</head>

<body>
<div>
<div class="headerlogin">
    <h1>AskMe?</h1>
</div>
    <section class="container text-center bg-light" style=" margin-top:70px;  width: fit-content; border: 2px solid #B5B5B5; padding: 50px 100px 30px 100px; border-radius:20px;" >
        <h4 class="title">Log-in</h4>
        <form class="" method="POST" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
            <label for="email">E-mail/Username:</label></br>
            <input type="text" class="text-danger1" id="email" name="email" value="<?php echo htmlspecialchars($email)?>"></br>
            <div class="text-danger"><?php echo $errors['name'];?></div>

            <label for="password">Password:</label></br>
            <input type="password" class="text-danger1" id="password" name="password"></br>
            <div class="text-danger"><?php echo $errors['password'];?></div>

            <div class="center" style="margin:10px; margin-top:40px;">
                <input type="submit" name="submit" value="Submit" class="button_slide slide_left">
            </div>
            <div>
                <input type="submit" name="create" value="Create a new account" class="button_slide slide_left logout">
            </div>
        </form>
    </section>
</div>
</body>

<footer>
    <p>Copyright © 2021</p>
    <p>D.Rojnić, J.Prpić, D.Dražetić</p>
</footer>

</html>