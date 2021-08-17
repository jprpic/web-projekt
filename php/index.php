<?php
if(!isset($_COOKIE['user'])){
    header('Location:./login.php');
}

if(isset($_POST['logout'])){
    setcookie("user",$_COOKIE['user'], time() - 3600 , "/");
    header('Location:./login.php');
}

if(isset($_POST['createquestion'])){
    header('Location:./create-question.php');
}

if(isset($_POST['userquestions'])){
    header('Location:./user-questions.php');
}

?>


<!DOCTYPE html>
<html>

<head>
    <title>Home Page</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
</head>

<body>
    <section class="container bg-light text-right">
        <form action="" method="POST">
            <input type="submit" name="createquestion" value="Create a Question" class="btn btn-primary text-white">
            <input type="submit" name="userquestions" value="Your Questions" class="btn btn-primary text-white">
            <input type="submit" name="logout" value="Log Out" class="btn btn-danger text-white">
        </form>
    </section>
</body>
</html>