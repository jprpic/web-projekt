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
require_once('./managers/comment-manager.php');
require_once('./managers/account-manager.php');

$conn = DBConfig::getConnection();
$accountManager = new AccountManager($conn);
$userID = $_GET['userID'];

if(!$accountManager->exists($userID)){
    header('Location:./missing-data.php');
}

$commentManager = new CommentManager($conn);

if(isset($_POST['removeReply'])){
    $commentManager->removeComment($_POST['removeReply'],$commentManager::TYPE_REPLY);
}
if(isset($_POST['removeComment'])){
    $commentManager->removeComment($_POST['removeComment'],$commentManager::TYPE_COMMENT);
}


require_once('./managers/answer-manager.php');
require_once('./managers/question-manager.php');


$questionManager = new QuestionManager($conn);
$answerManager = new AnswerManager($conn);
$isOwner = ($userID == $_SESSION['userID'] || isset($_SESSION['admin']));
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
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <link rel="stylesheet" href="styles.css">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    </head>
    <div class="header">
        <h1 class="logo">AskMe?</h1>
        <input type="checkbox" id="nav-toggle" class="nav-toggle">
        <nav class="nav">
            <ul>
                <li>
                    <form action="./index.php" method="post" >
                        <button type="submit" class="button_slide slide_left "><span class="text">Home</span></button>
                    </form>
                </li>
                <li>
                    <form action="./create-question.php" method="post" >
                        <button type="submit" class="button_slide slide_left"><span class="text">Create a Question</span></button>
                    </form>
                </li>
                <li>
                    <form action="./user-questions.php" method="get" >
                        <button type="submit" name="userID" value="<?= htmlspecialchars($_SESSION['userID']);?>" class="button_slide slide_left active1"><span class="text">your profile</span></button>
                    </form>
                </li>
                <li><form action="" method="POST">
                        <button type="submit" name="logout" value="Log Out" class="button_slide slide_left logout"><span class="text">Log out</span></button>

                    </form></li>
            </ul>
        </nav>
        <label for="nav-toggle" class="nav-toggle-label">
            <span></span>
        </label>
        <p class="username">Hi, <?= htmlspecialchars($accountManager->getUsername($_SESSION['userID']));?>!</p>
    </div>

    <body>
       
    <section style= "padding-top:100px;" class="questionsBox">

<div class="userInfo"  >
    <p style="margin:32px; font-size:32px; border: 3px solid white;"><?= htmlspecialchars($userName);?></p>
    <div style="width:120px; ">
        </br>
        <?= 'Questions: ' . htmlspecialchars($questionCount) . '</br>' ?>
        <?= 'Answers: ' . htmlspecialchars($answerCount) . '</br>' ?>
        <?= 'Comments: ' . htmlspecialchars($commentCount) . '</br>' ?>
    </div>
</div>

            <div class="boxWithQ">

                <div class="d-flex justify-content-center" style="margin:8px;">
                    <form action="./user-questions.php" method="get" style="margin:4px;">
                        <button type="submit" name="userID" value=<?= htmlspecialchars($userID);?> class="button_slide slide_left">Questions</button>
                    </form>
                    <form action="./user-answers.php" method="get" style="margin:4px;">
                        <button type="submit" name="userID" value=<?= htmlspecialchars($userID);?> class="button_slide slide_left">Answers</button>
                    </form>
                    <form action="./user-comments.php" method="get" style="margin:4px;">
                        <button type="submit" name="userID" value=<?= htmlspecialchars($userID);?> class="button_slide slide_left active1">Comments</button>
                    </form>
                </div>

                <div style="margin:0px 12px;">
                    <div class="text-center" style="font-size:32px;">Comments</div>
                    <table class="table table-striped text-center">
                        <thead>

                        </thead>
                        <tbody class="text-center">
                            <?php foreach($comments as &$comment):?>
                                <?php $isReply = $comment['type']==$commentManager::TYPE_REPLY;?>
                                <tr>
                                    <td>
                                        <form action="./question.php" method="get">
                                            <button type="submit" name="questionID" value=<?= htmlspecialchars($comment['questionID']);?> class="buttonQuestion question1"><?=  htmlspecialchars($questionManager->getQuestion($comment['questionID'])) ?></button>
                                        </form>
                                        <div class="p-2">
                                            <?=  htmlspecialchars($comment['comment']);?>
                                        </div>
                                    </td>
                                    <?php if($isOwner):?>
                                        <td class="align-middle">
                                        <form action="" method="post"  >
                                            <button type="submit" name=<?php if($isReply){echo "removeReply";}else{echo "removeComment";}?> value=<?= htmlspecialchars($comment['id']) ?>
                                            class="btn btn-outline-danger btn-sm">x</button>
                                        </form>
                                        </td>
                                    <?php endif;?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </section>
        
    </body>
    <footer>
    <p>Copyright © 2021</p>
    <p>D.Rojnić, J.Prpić, D.Dražetić</p>
</footer>
</html>