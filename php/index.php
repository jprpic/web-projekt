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

require_once('./dbconfig.php');
require_once('./managers/question-manager.php');

$questionManager = new QuestionManager(DBConfig::getConnection());
$availableQuestions = $questionManager->getAvailableQuestions($_SESSION['userID']);


if(isset($_POST['yesanswer'])){
    $questionManager->answerQuestion($_POST['yesanswer'],$_SESSION['userID'],"yes");
    header("Refresh:0");
}

if(isset($_POST['noanswer'])){
    $questionManager->answerQuestion($_POST['noanswer'],$_SESSION['userID'],"no");
    header("Refresh:0");
}


?>


<!DOCTYPE html>
<html>

<head>
    <title>Home Page</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
</head>

<body>
    <section class="d-flex justify-content-between bg-light text-right">
        <a href="./index.php"><button class="btn btn-primary text-white" style="margin:4px;">Home</button></a>
        <div class="d-flex justify-content-end" style="margin:4px;">
            <a href="./create-question.php"><button class="btn btn-primary text-white">Create a Question</button></a>
            <form action="./user-questions.php" method="get" style="margin:0px 4px;">
                <button type="submit" name="userID" value=<?= $_SESSION['userID'];?> class="btn btn-primary text-white">Your profile</button>
            </form>
            <form action="" method="POST">
                <input type="submit" name="logout" value="Log Out" class="btn btn-danger text-white">
            </form>
        </div>
    </section>

    <section>
    <table class="table table-striped text-center">
        <thead>
                <tr>
                    <th scope="col">Question</th>
                    <th scope="col">Yes</th>
                    <th scope="col">No</th>
                </tr>
            </thead>
        <tbody>
            <?php while ($question = $availableQuestions->fetch()) : ?>
                <tr>
                    <td>
                        <form action="question.php" method="get">
                            <button type="submit" name="questionID" value=<?= $question['id'] ?> class="btn btn-danger text-white"><?= htmlspecialchars($questionManager->getQuestion($question['id'])) ?></button>
                        </form>
                    </td>
                    <td>
                        <form action="" method="POST">
                            <button type="submit" name="yesanswer" value=<?=$question['id']?> class="btn btn-primary text-white">Yes</button>
                        </form>
                    </td>
                    <td>
                        <form action="" method="POST">
                            <button type="submit" name="noanswer" value=<?=$question['id']?> class="btn btn-danger text-white">No</button>
                        </form>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    </section>
    
</body>
</html>