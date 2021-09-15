<?php
session_start();
if(!isset($_SESSION['userID'])){
    header('Location:./login.php');
}

if(isset($_POST['logout'])){
    unset($_SESSION["userID"]);
    if(isset($_SESSION['admin'])){
        unset($_SESSION['admin']);
    }
    header('Location:./login.php');
}

require_once('./dbconfig.php');
require_once('./managers/account-manager.php');
$conn = DBConfig::getConnection();
$userID = $_SESSION['userID'];
$accountManager = new AccountManager($conn);
$userName = $accountManager->getUsername($userID);
?>

<!DOCTYPE html>
<html>

    <head>
        <title>Missing data</title>
        <link rel="stylesheet" href="styles.css">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    </head>

    <body>
    <div class="header">
        <h1 class="logo">AskMe?</h1>
        <input type="checkbox" id="nav-toggle" class="nav-toggle">
        <nav class="nav">
            <ul>
                <li>
                    <form action="./index.php" method="post" >
                        <button type="submit" class="button_slide slide_left active1"><span class="text">Home</span></button>
                    </form>
                </li>
                <li>
                    <form action="./create-question.php" method="post" >
                        <button type="submit" class="button_slide slide_left"><span class="text">Create a Question</span></button>
                    </form>
                </li>
                <li>
                    <form action="./user-questions.php" method="get" >
                        <button type="submit" name="userID" value="<?= htmlspecialchars($_SESSION['userID']);?>" class="button_slide slide_left"><span class="text">your profile</span></button>
                    </form>
                </li>
                <li><form action="" method="POST">
                        <button type="submit" name="logout" value="Log Out" class="button_slide slide_left logout"><span class="text">Log out</span></button>

                    </form></li>
            </ul>
        </nav>
        <label for="nav-toggle" class="nav-toggle-label">
            <span></span>
        </label>
        <p class="username">Hi, <?= htmlspecialchars($userName);?>!</p>
    </div>

        <section class="text-center bg-light">
            <br><br><br><br>
            <h4 class="title">Sorry, there's nothing here.</h4>
        </section>

    </body>
</html>