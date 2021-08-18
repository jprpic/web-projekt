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
    <section class="container bg-light text-right">
        <form action="" method="POST">
            <input type="submit" name="createquestion" value="Create a Question" class="btn btn-primary text-white">
            <input type="submit" name="userquestions" value="Your Questions" class="btn btn-primary text-white">
            <input type="submit" name="useranswers" value="Your Answers" class="btn btn-primary text-white">
            <input type="submit" name="logout" value="Log Out" class="btn btn-danger text-white">
        </form>
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
                    <td><?= htmlspecialchars($question['question']) ?></td>
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