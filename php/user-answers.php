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

require_once('./managers/answer-manager.php');

$conn = DBConfig::getConnection();
$answerManager = new AnswerManager($conn);
$ownerID = $_SESSION['userID'];


if(isset($_POST['remove'])){
    $answerManager->removeAnswer($_POST['remove'],$ownerID);
}

require_once('./managers/question-manager.php');
require_once('./managers/account-manager.php');
require_once('./managers/comment-manager.php');

$questionManager = new QuestionManager($conn);
$accountManager = new AccountManager($conn);
$commentManager = new CommentManager($conn);
$userID = $_GET['userID'];
$isOwner = ($userID == $_SESSION['userID'] || isset($_SESSION['admin']));
$questionIDs = $answerManager->getAnsweredQuestionIDs($userID);
$userName = $accountManager->getUsername($userID);

$questionCount = $questionManager->countQuestions($userID);
$answerCount = $answerManager->countUserAnswers($userID);
$commentCount = $commentManager->countComments($userID);

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

        <section class="d-flex justify-content-around">

            <div class="d-flex align-items-center">
                <p style="margin:32px; font-size:32px;"><?= $userName;?></p>
                <div>
                    <?= 'Questions: ' . $questionCount . '</br>' ?>
                    <?= 'Answers: ' . $answerCount . '</br>' ?>
                    <?= 'Comments: ' . $commentCount . '</br>' ?>
                </div>
            </div>

            <div>

                <div class="d-flex justify-content-center" style="margin:8px;">
                    <form action="./user-questions.php" method="get" style="margin:4px;">
                        <button type="submit" name="userID" value=<?= $userID;?> class="btn btn-primary text-white">Questions</button>
                    </form>
                    <form action="./user-answers.php" method="get" style="margin:4px;">
                        <button type="submit" name="userID" value=<?= $userID;?> class="btn btn-primary text-white">Answers</button>
                    </form>
                    <form action="./user-comments.php" method="get" style="margin:4px;">
                        <button type="submit" name="userID" value=<?= $userID;?> class="btn btn-primary text-white">Comments</button>
                    </form>
                </div>

                <div class="text-center" style="font-size:32px;">Answers</div>
                <table class="table table-striped text-center">
                <thead>
                    <tr>
                        <th scope="col">Question</th>
                        <th scope="col">Answer</th>
                        <?php if($isOwner):?>
                                <th scope="col"></th>
                        <?php endif;?>
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
                                <?= htmlspecialchars($answerManager->getUserAnswer($questionID,$userID));?>
                            </td>
                            <?php if($isOwner):?>
                                    <td>
                                        <form action="" method="post">
                                            <button type="submit" name="remove" value=<?= $questionID; ?> class="btn btn-outline-danger btn-sm" style="font-size:12px;">x</button>
                                        </form>
                                    </td>
                            <?php endif;?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            </div>

        </section>
        

        
        
    </body>
</html>