<?php
session_start();
if(!isset($_SESSION['userID'])){
    header('Location:./login.php');
}

$error='';

if(isset($_POST['submit'])){
    require_once('./question-manager.php');
    require_once('./dbconfig.php');
    $questionManager = new QuestionManager(DBConfig::getConnection());
    $question = $_POST['question'];
    $error = $questionManager->checkQuestion($question);

    if(!$error){
        $questionManager->saveQuestion($question,$_SESSION['userID']);
        header('location:index.php');
    }
}
?>


<!DOCTYPE html>
<html>

    <head>
        <title>Create a Question</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    </head>

    <body>

        <section class="container text-center bg-light">
            <h4 class="title">Create a Question</h4>
            <form class="" method="POST" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
                <label for="email">Question:</label></br>
                <textarea id="question" name="question" rows="3" cols="100" style="padding:4px 0px 0px 8px;"></textarea>
                <div class="text-danger"> <?= $error ?> </div>

                <input type="submit" id="submit" name="submit" value ="Submit" class="btn btn-primary text-white" style="margin-top:20px;"></br>
            </form>
        </section>

    </body>
</html>