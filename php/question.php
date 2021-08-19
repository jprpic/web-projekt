<?php
session_start();
if(!isset($_SESSION['userID'])){
    header('Location:./index.php');
}

require_once('./dbconfig.php');
require_once('./managers/question-manager.php');
require_once('./managers/answer-manager.php');
require_once('./managers/account-manager.php');

$conn = DBConfig::getConnection();
$questionManager = new QuestionManager($conn);
$answerManager = new AnswerManager($conn);
$accountManager = new AccountManager($conn);
$questionID = $_GET['questionID'];
$questionOwnerID = $questionManager->getOwner($questionID); 
$questionOwner = $accountManager->getUsername($questionOwnerID);

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
    <section class="d-flex justify-content-between bg-light text-right">
        <a href="./index.php"><button class="btn btn-primary text-white" style="margin:4px;">Home</button></a>
        <form action="" method="POST" style="margin:4px;">
            <input type="submit" name="createquestion" value="Create a Question" class="btn btn-primary text-white">
            <input type="submit" name="userquestions" value="Your Questions" class="btn btn-primary text-white">
            <input type="submit" name="useranswers" value="Your Answers" class="btn btn-primary text-white">
            <input type="submit" name="logout" value="Log Out" class="btn btn-danger text-white">
        </form>
    </section>

    <div class="text-center" style="padding: 64px;font-size: 32px;">
        <?= $question; ?>
    </div>
    <div class="d-flex justify-content-center">
        <div class="text-left bg-danger text-white" style="padding: 64px;font-size: 32px;margin:32px;">
            <?= $answerCount['no']; ?>
        </div>
        <div class="text-right bg-success text-white" style="padding: 64px;font-size: 32px;margin:32px;">
            <?= $answerCount['yes']; ?>
        </div>
    </div>

    <section class="bg-light text-right" style="margin:0px 8px 0px 0px;">
        <form action="./user.php" method="get">
            <div style="display: inline;">Question from: </div>
            <button type="submit" class="btn btn-info text-white" style="margin:4px;" name="userID" value=<?= $questionOwnerID; ?>><?= $questionOwner; ?></button>
        </form>
    </section>

    

</body>

</html>

