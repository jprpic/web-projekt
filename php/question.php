<?php
session_start();
if(!isset($_SESSION['userID'])){
    header('Location:./index.php');
}

if(isset($_POST['logout'])){
    unset($_SESSION["userID"]);
    header('Location:./login.php');
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
$userID = $_SESSION['userID'];

$userAnswer = $answerManager->getUserAnswer($questionID,$userID);
$isQuestionOwner = $questionOwnerID == $userID;

if(isset($_POST['yesanswer'])){
    if(!$userAnswer){
        $answerManager->answerQuestion($questionID,$userID,"yes");
    }
    else{
        if($userAnswer=="no"){
            $answerManager->changeAnswer($questionID,$userID,"yes");
        }
    }
    header("Refresh:0");
}

if(isset($_POST['noanswer'])){
    if(!$userAnswer){
        $answerManager->answerQuestion($questionID,$userID,"no");
    }
    else{
        if($userAnswer=="yes"){
            $answerManager->changeAnswer($questionID,$userID,"no");
        }
    }
    header("Refresh:0");
}

$question = $questionManager->getQuestion($questionID);
$answerCount = $answerManager->countAnswers($questionID);

unset($conn);

?>

<!DOCTYPE html>
<html>

<head>
    <title><?= htmlspecialchars($question); ?></title>
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

    <div class="text-center" style="padding: 64px;font-size: 32px;">
        <?= htmlspecialchars($question); ?>
        <?php if($userAnswer):?>
            <p>You voted <span class=<?php if($userAnswer=="no"){echo "text-danger";}else{echo "text-success";}?>><?= $userAnswer; ?></span></p>
        <?php endif;?>
    </div>
    <div class="d-flex justify-content-center">
        <?php if(!$isQuestionOwner):?>
            <form  action="" method="post">
                <button type="submit" name="noanswer" value=<?= $questionID?> class="btn btn-danger text-white">No</button>
            </form>
        <?php endif;?>
        <div class="text-left bg-danger text-white" style="padding: 64px;font-size: 32px;margin:32px;">
            <?= $answerCount['no']; ?>
        </div>
        <div class="text-right bg-success text-white" style="padding: 64px;font-size: 32px;margin:32px;">
            <?= $answerCount['yes']; ?>
        </div>
        <?php if(!$isQuestionOwner):?>
            <form action="" method="post">
                <button type="submit" name="yesanswer" value=<?= $questionID?> class="btn btn-success text-white">Yes</button>
            </form>
        <?php endif;?>
    </div>

    <section class="bg-light text-right" style="margin:0px 8px 0px 0px;">
        <form action="./user-questions.php" method="get">
            <div style="display: inline;">Question from: </div>
            <button type="submit" class="btn btn-info text-white" style="margin:4px;" name="userID" value=<?= $questionOwnerID; ?>><?= $questionOwner; ?></button>
        </form>
    </section>

    

</body>

</html>

