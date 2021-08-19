<?php
session_start();
if(!isset($_SESSION['userID'])){
    header('Location:./login.php');
}

if(isset($_POST['logout'])){
    unset($_SESSION["userID"]);
    header('Location:./login.php');
}

if(isset($_POST['createquestion'])){
    header('Location:./create-question.php');
}

if(isset($_POST['userquestions'])){
    header('Location:./user-questions.php');
}
if(isset($_POST['useranswers'])){
    header('Location:./user-answers.php');
}

$error='';

if(isset($_POST['submit'])){
    require_once('./managers/question-manager.php');
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
        <section class="d-flex justify-content-between bg-light text-right">
            <a href="./index.php"><button class="btn btn-primary text-white" style="margin:4px;">Home</button></a>
            <form action="" method="POST" style="margin:4px;">
                <input type="submit" name="createquestion" value="Create a Question" class="btn btn-primary text-white">
                <input type="submit" name="userquestions" value="Your Questions" class="btn btn-primary text-white">
                <input type="submit" name="useranswers" value="Your Answers" class="btn btn-primary text-white">
                <input type="submit" name="logout" value="Log Out" class="btn btn-danger text-white">
            </form>
        </section>

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