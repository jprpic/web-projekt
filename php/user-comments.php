<?php
session_start();
if(!isset($_SESSION['userID'])){
    header('Location:./login.php');
}

if(isset($_POST['logout'])){
    unset($_SESSION["userID"]);
    header('Location:./login.php');
}

require_once('./dbconfig.php');
require_once('./managers/comment-manager.php');

$conn = DBConfig::getConnection();
$commentManager = new CommentManager($conn);

if(isset($_POST['removeReply'])){
    $commentManager->removeComment($_POST['removeReply'],$commentManager::TYPE_REPLY);
}
if(isset($_POST['removeComment'])){
    $commentManager->removeComment($_POST['removeComment'],$commentManager::TYPE_COMMENT);
}


require_once('./managers/answer-manager.php');
require_once('./managers/account-manager.php');
require_once('./managers/question-manager.php');


$questionManager = new QuestionManager($conn);
$answerManager = new AnswerManager($conn);
$accountManager = new AccountManager($conn);
$userID = $_GET['userID'];
$isOwner = $userID == $_SESSION['userID'];
$userName = $accountManager->getUsername($userID);

$questionCount = $questionManager->countQuestions($userID);
$answerCount = $answerManager->countUserAnswers($userID);
$commentCount = $commentManager->countComments($userID);

$questionComments = $commentManager->getUserQuestionComments($userID);
$childComments = $commentManager->getUserChildComments($userID);

$comments = array();

foreach($questionComments as &$questionComment){
    $comment = array(
        'type' => $commentManager::TYPE_COMMENT,
        'id' => $questionComment['id'],
        'questionID' => $questionComment['questionID'],
        'comment' => $questionComment['comment'],
        'creationTime' => strtotime($questionComment['creationTime'])
    );
    array_push($comments,$comment);
}

foreach($childComments as &$childComment){
    $comment = array(
        'type' => $commentManager::TYPE_REPLY,
        'id' => $childComment['id'],
        'questionID' => $commentManager->getQuestionID($childComment['parentID']),
        'comment' => $childComment['comment'],
        'creationTime' => strtotime($childComment['creationTime'])
    );
    array_push($comments,$comment);
}


function date_compare($element1, $element2) {
    $datetime1 = $element1['creationTime'];
    $datetime2 = $element2['creationTime'];
    return $datetime2 - $datetime1;
} 
  
usort($comments, 'date_compare');

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

                <div style="margin:0px 12px;">
                    <div class="text-center" style="font-size:32px;">Comments</div>
                    <table class="table table-striped text-center">
                        <tbody class="text-left">
                            <?php foreach($comments as &$comment):?>
                                <?php $isReply = $comment['type']==$commentManager::TYPE_REPLY;?>
                                <tr>
                                    <td>
                                        <form action="./question.php" method="get">
                                            <button type="submit" name="questionID" value=<?= $comment['questionID'];?> class="btn btn-danger text-white btn-sm"><?= $questionManager->getQuestion($comment['questionID']) ?></button>
                                        </form>
                                        <div class="d-flex p-2">
                                            <span style="margin-left:16px;"><?= $comment['comment'];?></span>
                                            <form action="" method="post">
                                                <button type="submit" name=<?php if($isReply){echo "removeReply";}else{echo "removeComment";}?> value=<?= $comment['id'] ?>
                                                class="btn btn-outline-danger btn-sm" style="margin-left:8px;">delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </section>
        
    </body>
</html>