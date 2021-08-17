<?php

if(isset($_POST['logout'])){
    setcookie("user",$_COOKIE['user'], time() - 3600 , "/");
    header('Location:./login.php');
}

if(!isset($_COOKIE['user'])){
    header('Location:./login.php');
}



?>


<!DOCTYPE html>
<html>

<head>
    <title>Home Page</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
</head>

<body>

<form action="" method="POST">
    <input type="submit" name="logout" value="Log Out" class="btn btn-primary text-white">
</form>

</body>


</html>