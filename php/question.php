<?php
session_start();
if(!isset($_SESSION['userID'])){
    header('Location:./index.php');
}

require_once('./dbconfig.php');
require_once('./managers/question-manager.php');
require_once('./managers/answer-manager.php');

$conn = DBConfig::getConnection();
$questionManager = new QuestionManager($conn);
$answerManager = new AnswerManager($conn);
$questionID = $_GET['questionID'];

$question = $questionManager->getQuestion($questionID);
$answerCount = $answerManager->countAnswers($questionID);
?>

<!DOCTYPE html>
<html>

<head>
    <title><?= $question; ?></title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
</head>

<body>
    <div class="text-center" style="padding: 64px;font-size: 32px;">
        <?= $question; ?>
    </div>
    <div class="d-flex justify-content-center">
        <div class="text-left bg-danger text-white" style="padding: 64px;font-size: 32px;margin:32px;">
            <?= $answerCount['no']; ?>
        </div>
        <div class="text-right bg-primary text-white" style="padding: 64px;font-size: 32px;margin:32px;">
            <?= $answerCount['yes']; ?>
        </div>
    </div>

</body>

</html>

