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
require_once('./managers/comment-manager.php');

$questionID = $_GET['questionID'];
$userID = $_SESSION['userID'];
$conn = DBConfig::getConnection();
$commentManager = new CommentManager($conn);

if(isset($_POST['commentSubmit'])){
    $comment = $_POST['commentText'];
    $commentManager->answerQuestion($questionID,$userID,$comment);
    header("Refresh:0");
}

if(isset($_POST['removeQuestionComment'])){
    $commentManager->removeComment($_POST['removeQuestionComment'],$commentManager::TYPE_COMMENT);
    header("Refresh:0");
}

if(isset($_POST['removeChildComment'])){
    $commentManager->removeComment($_POST['removeChildComment'],$commentManager::TYPE_REPLY);
    header("Refresh:0");
}

if(isset($_POST['replyComment'])){
    $commentManager->replyToComment($userID,$_POST['replyComment']);
    header("Refresh:0");
}

if(isset($_POST['editQuestionComment'])){
    $commentManager->editComment($_POST['editQuestionComment'],$commentManager::TYPE_COMMENT);
    header("Refresh:0");
}

if(isset($_POST['editChildComment'])){
    $commentManager->editComment($_POST['editChildComment'],$commentManager::TYPE_REPLY);
    header("Refresh:0");
}

$questionComments = $commentManager->loadQuestionComments($questionID);

require_once('./managers/question-manager.php');
require_once('./managers/answer-manager.php');
require_once('./managers/account-manager.php');


$questionManager = new QuestionManager($conn);
$answerManager = new AnswerManager($conn);
$accountManager = new AccountManager($conn);

$questionOwnerID = $questionManager->getOwner($questionID); 
$questionOwner = $accountManager->getUsername($questionOwnerID);


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
                <button type="submit" name="noanswer" value=<?= $questionID?> class="btn btn-danger text-white"
                <?php if($userAnswer=="no"){echo "disabled";}?>>No</button>
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
                <button type="submit" name="yesanswer" value=<?= $questionID?> class="btn btn-success text-white"
                <?php if($userAnswer=="yes"){echo "disabled";}?>>Yes</button>
            </form>
        <?php endif;?>
    </div>

    <section class="bg-light text-right" style="margin:0px 8px 0px 0px;">
        <form action="./user-questions.php" method="get">
            <div style="display: inline;">Question from: </div>
            <button type="submit" class="btn btn-info text-white" style="margin:4px;" name="userID" value=<?= $questionOwnerID; ?>><?= $questionOwner; ?></button>
        </form>
    </section>

    <section class="d-flex p-2 text-left bg-light">
        <form method="POST" action="">
            <label for="email">Comment:</label></br>
            <textarea id="commentText" name="commentText" rows="2" cols="100" style="padding:4px 0px 0px 8px;"></textarea>
            <input type="submit" id="commentSubmit" name="commentSubmit" value ="Submit" class="btn btn-primary text-white" style="margin-top:20px;"></br>
        </form>
    </section>


    <section>
        <table class="table table-striped text-center">
            <tbody class="text-left">
                <?php foreach ($questionComments as &$questionComment) : ?>
                        <tr>
                            <td>
                                <?php if($questionComment['comment'] !="[deleted]"):?>
                                    <form action="./user-questions.php" method="get">
                                        <button type="submit" class="btn btn-info text-white btn-sm" name="userID" value=<?= $questionComment['userID']; ?>><?= $accountManager->getUsername($questionComment['userID']); ?></button>
                                    </form>

                                    <p><?= $questionComment['comment']; ?></p>

                                    <?php if($userID == $questionComment['userID']):?>

                                    
                                        <form action="" method="post" style="margin:-16px 0px 0px 0px;">
                                            <button type="submit" class="btn btn-outline-danger btn-sm" name="removeQuestionComment" value=<?= $questionComment['id']; ?>>delete</button>
                                            <button type="submit" class="btn btn-outline-secondary btn-sm" name="editQuestionComment" value=<?= $questionComment['id']; ?>>edit</button>
                                        </form>
                                    
                                    <?php else:?>
                                        <form action="" method="post" style="margin:-16px 0px 0px 0px;">
                                            <button type="submit" class="btn btn-outline-secondary btn-sm" name="replyComment" value=<?= $questionComment['id']; ?>>reply</button>
                                        </form>
                                    <?php endif;?> 


                                <?php else:?>
                                    <p class='text-muted font-italic'>[deleted]</p>
                                <?php endif;?>

                                <?php $childComments = $commentManager->loadChildComments($questionComment['id']);
                                foreach ($childComments as &$childComment):?>

                                    <div style="margin-left: 32px;">
                                        <?= $childComment['comment'];?>
                                        <?php if($userID == $childComment['userID']):?>
                                            <form action="" method="post">
                                                <button type="submit" class="btn btn-outline-danger btn-sm" name="removeChildComment" value=<?= $childComment['id']; ?>>delete</button>
                                                <button type="submit" class="btn btn-outline-secondary btn-sm" name="editChildComment" value=<?= $childComment['id']; ?>>edit</button>
                                            </form>
                                        <?php endif;?> 
                                    </div>
                                <?php endforeach; ?>
                            </td>
                        </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>


</body>

</html>

