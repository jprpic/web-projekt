<?php
session_start();
if(!isset($_SESSION['userID'])){
    header('Location:./login.php');
}

require_once('./managers/answer-manager.php');
require_once('./managers/question-manager.php');
require_once('./dbconfig.php');

$conn = DBConfig::getConnection();

$answerManager = new AnswerManager($conn);
$questionManager = new QuestionManager($conn);
$questionIDs = $answerManager->getAnsweredQuestionIDs($_SESSION['userID']);

unset($conn);
?>

<!DOCTYPE html>
<html>

    <head>
        <title>Your Questions</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    </head>

    <body>

        <table class="table table-striped text-center">
            <thead>
                <tr>
                    <th scope="col">Question</th>
                    <th scope="col">Answer</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($questionIDs as &$questionID): ?>
                    <tr>
                        <td>
                            <form action="question.php" method="get">
                            <button type="submit" name="questionID" value=<?= $questionID ?> class="btn btn-danger text-white"><?= htmlspecialchars($questionManager->getQuestion($questionID)) ?></button>
                            </form>
                        </td>
                        <td>
                            <?= $answerManager->getUserAnswer($questionID,$_SESSION['userID']);?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
    </body>
</html>