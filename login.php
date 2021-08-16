<?php

if(isset($_POST['create'])){
    header("Location:./create-account.php");
}



$email = $password = "";
$errors = array('email'=>'', 'password'=>'');


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
            <div class="text-danger"><?php echo $errors['email'];?></div>

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