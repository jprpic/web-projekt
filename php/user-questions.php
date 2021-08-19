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
require_once('./managers/answer-manager.php');

$conn = DBConfig::getConnection();

$questionManager = new QuestionManager($conn);
$answerManager = new AnswerManager($conn);
$questionIDs = $questionManager->getUserQuestionIDs($_SESSION['userID']);

unset($conn);
?>


<!DOCTYPE html>
<html>

    <head>
        <title>Your Questions</title>
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

        <table class="table table-striped text-center">
            <thead>
                <tr>
                    <th scope="col">Question</th>
                    <th scope="col">Yes</th>
                    <th scope="col">No</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($questionIDs as &$questionID):
                    $question = $questionManager->getQuestion($questionID);
                    $answerCount = $answerManager->countAnswers($questionID);?>
                    <tr>
                        <td>
                            <form action="question.php" method="get">  
                                <button type="submit" name="questionID" value=<?= $questionID ?> class="btn btn-danger text-white"><?= htmlspecialchars($question) ?></button>
                            </form>
                        </td>
                        <td>
                            <?php echo $answerCount['yes'];
                            echo ' (' . round($questionManager->getPositivePercentage($answerCount['yes'],$answerCount['no'])) . '%)';?>
                        </td>
                        <td>
                        <?php echo $answerCount['no'];
                            echo ' (' . round($questionManager->getNegativePercentage($answerCount['yes'],$answerCount['no'])) . '%)';?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
    </body>
</html>