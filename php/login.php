<?php

$email = $password = "";
$errors = array('name'=>'','password'=>'');

if(isset($_POST['create'])){
    header("Location:./create-account.php");
}
if(isset($_POST['submit'])){
    require_once('./account-manager.php');
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
        setcookie("user", $user['id'], time() + (86400 * 30), "/");
        header('Location:index.php');
    }
}
?>


<!DOCTYPE html>
<html>

<head>
<title>Log in</title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
</head>

<body>
<section class="container text-center bg-light">
        <h4 class="title">Log-in</h4>
        <form class="" method="POST" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
            <label for="email">E-mail/Username:</label></br>
            <input type="text" id="email" name="email" value="<?php echo htmlspecialchars($email)?>"></br>
            <div class="text-danger"><?php echo $errors['name'];?></div>

            <label for="password">Password:</label></br>
            <input type="password" id="password" name="password"></br>
            <div class="text-danger"><?php echo $errors['password'];?></div>

            <div class="center" style="margin:10px;">
                <input type="submit" name="submit" value="Submit" class="btn btn-primary text-white">
            </div>
            <div>
                <input type="submit" name="create" value="Create a new account">
            </div>
        </form>
    </section>
</body>

</html>