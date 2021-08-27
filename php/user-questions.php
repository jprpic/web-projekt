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
require_once('./managers/question-manager.php');
require_once('./managers/account-manager.php');

$conn = DBConfig::getConnection();
$accountManager = new AccountManager($conn);
$userID = $_GET['userID'];

if(!$accountManager->exists($userID)){
    header('Location:./missing-data.php');
}

$questionManager = new QuestionManager($conn);
if(isset($_POST['remove'])){
    $questionManager->removeQuestion($_POST['remove']);
}

require_once('./managers/answer-manager.php');

require_once('./managers/comment-manager.php');

$commentManager = new CommentManager($conn);
$answerManager = new AnswerManager($conn);


$questionIDs = $questionManager->getUserQuestionIDs($userID);
$isOwner = ($userID == $_SESSION['userID'] || isset($_SESSION['admin']));
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
                    <button type="submit" name="userID" value=<?= htmlspecialchars($_SESSION['userID']);?> class="btn btn-primary text-white">Your profile</button>
                </form>
                <form action="" method="POST">
                    <input type="submit" name="logout" value="Log Out" class="btn btn-danger text-white">
                </form>
            </div>
        </section>

        <section class="d-flex justify-content-around">

            <div class="d-flex align-items-center">
                <p style="margin:32px; font-size:32px;"><?= htmlspecialchars($userName);?></p>
                <div>
                    <?= 'Questions: ' . htmlspecialchars($questionCount) . '</br>' ?>
                    <?= 'Answers: ' . htmlspecialchars($answerCount) . '</br>' ?>
                    <?= 'Comments: ' . htmlspecialchars($commentCount) . '</br>' ?>
                </div>
            </div>

            <div>

                <div class="d-flex justify-content-center" style="margin:8px;">
                    <form action="./user-questions.php" method="get" style="margin:4px;">
                        <button type="submit" name="userID" value=<?= htmlspecialchars($userID);?> class="btn btn-primary text-white">Questions</button>
                    </form>
                    <form action="./user-answers.php" method="get" style="margin:4px;">
                        <button type="submit" name="userID" value=<?= htmlspecialchars($userID);?> class="btn btn-primary text-white">Answers</button>
                    </form>
                    <form action="./user-comments.php" method="get" style="margin:4px;">
                        <button type="submit" name="userID" value=<?= htmlspecialchars($userID);?> class="btn btn-primary text-white">Comments</button>
                    </form>
                </div>

                <div class="text-center" style="font-size:32px;">Questions</div>
                <table class="table table-striped text-center">
                    <thead>
                        <tr>
                            <th scope="col">Question</th>
                            <th scope="col">Yes</th>
                            <th scope="col">No</th>
                            <?php if($isOwner):?>
                                <th scope="col"></th>
                            <?php endif;?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($questionIDs as &$questionID):
                            $question = $questionManager->getQuestion($questionID);
                            $answerCount = $answerManager->countAnswers($questionID);?>
                            <tr>
                                <td>
                                    <form action="question.php" method="get">  
                                        <button type="submit" name="questionID" value=<?= htmlspecialchars($questionID); ?> class="btn btn-danger text-white"><?= htmlspecialchars($question) ?></button>
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
                                <?php if($isOwner):?>
                                    <td>
                                        <form action="" method="post">
                                            <button type="submit" name="remove" value=<?= htmlspecialchars($questionID); ?> class="btn btn-outline-danger btn-sm" style="font-size:12px;">x</button>
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