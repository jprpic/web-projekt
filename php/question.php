<?php
session_start();
if(!isset($_SESSION['userID'])){
    header('Location:./index.php');
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
require_once('./managers/question-manager.php');
require_once('./managers/answer-manager.php');

$questionID = $_GET['questionID'];
$userID = $_SESSION['userID'];
$conn = DBConfig::getConnection();
$commentManager = new CommentManager($conn);
$accountManager = new AccountManager($conn);
$questionManager = new QuestionManager($conn);
$answerManager = new AnswerManager($conn);

if(!$questionManager->exists($questionID)){
    header('Location:./missing-data.php');
}

if(isset($_POST['removeQuestion'])){
    $questionManager->removeQuestion($questionID);
    header('Location:./index.php');
}

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
    $comment = "This is a reply!";
    if(isset($_POST['replyToUserID'])){
        $comment = '@' . $accountManager->getUsername($_POST['replyToUserID']) . ' ' . $comment;
    }
    $commentManager->replyToComment($userID,$_POST['replyComment'],$comment);
}

if(isset($_POST['editQuestionComment'])){
    $commentManager->editComment($_POST['editQuestionComment'],$commentManager::TYPE_COMMENT);
    header("Refresh:0");
}

if(isset($_POST['editChildComment'])){
    $commentManager->editComment($_POST['editChildComment'],$commentManager::TYPE_REPLY);
    header("Refresh:0");
}

$questionComments = $commentManager->getQuestionComments($questionID);


$questionOwnerID = $questionManager->getOwner($questionID); 
$questionOwner = $accountManager->getUsername($questionOwnerID);


$userAnswer = $answerManager->getUserAnswer($questionID,$userID);
$isQuestionOwner = $questionOwnerID == $userID;
$isAdmin = isset($_SESSION['admin']);

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
                <button type="submit" name="userID" value=<?= htmlspecialchars($_SESSION['userID']);?> class="btn btn-primary text-white">Your profile</button>
            </form>
            <form action="" method="POST">
                <input type="submit" name="logout" value="Log Out" class="btn btn-danger text-white">
            </form>
        </div>
    </section>

    <div class="text-center" style="padding: 64px;font-size: 32px;">
        <?= htmlspecialchars($question); ?>
        <?php if($userAnswer):?>
            <p>You voted <span class=<?php if($userAnswer=="no"){echo "text-danger";}else{echo "text-success";}?>><?= htmlspecialchars($userAnswer); ?></span></p>
        <?php endif;?>
    </div>
    <div class="d-flex justify-content-center">
        <form action="./question.php" method="get">
            <button type="submit" name="questionID" class="btn btn-info btn-sm" <?php
            $previousQuestionID = htmlspecialchars($questionManager->getPreviousQuestion($userID,$questionID));
            if($previousQuestionID){echo "value=$previousQuestionID";}
            else{echo "disabled";} ?>>previous</button>
        </form>
        <form action="" method="post">
                <button type="submit" name="noanswer" style="margin:0px 8px;"value=<?= htmlspecialchars($questionID);?> class="btn btn-danger text-white"
                <?php if($userAnswer=="no" || $isQuestionOwner){echo "disabled";}?>>No</button>
        </form>
        <div class="text-left bg-danger text-white" style="padding: 64px;font-size: 32px;margin:32px;">
            <?= htmlspecialchars($answerCount['no']); ?>
        </div>
        <div class="text-right bg-success text-white" style="padding: 64px;font-size: 32px;margin:32px;">
            <?= htmlspecialchars($answerCount['yes']); ?>
        </div>
        <form action="" method="post">
            <button type="submit" name="yesanswer" value=<?= htmlspecialchars($questionID); ?> class="btn btn-success text-white"
            <?php if($userAnswer=="yes" || $isQuestionOwner){echo "disabled";}?>>Yes</button>
        </form>
        <form action="./question.php" method="get">
            <button type="submit" name="questionID"  style="margin:0px 8px;" class="btn btn-info btn-sm"<?php
            $nextQuestionID = htmlspecialchars($questionManager->getNextQuestion($userID,$questionID));
            if($nextQuestionID){echo "value=$nextQuestionID";}
            else{echo "disabled";} ?>>next</button>
        </form>
    </div>

    <section class="bg-light text-right d-flex justify-content-end" style="margin:0px 8px 0px 0px;">
        <form action="./user-questions.php" method="get">
            <div style="display: inline;">Question from: </div>
            <button type="submit" class="btn btn-info text-white btn-sm" style="margin:4px;" name="userID" value=<?= htmlspecialchars($questionOwnerID); ?>><?= htmlspecialchars($questionOwner); ?></button>
        </form>
        <?php if($isQuestionOwner || $isAdmin):?>
            <form action="" method="post">
                <button type="submit" class="btn btn-outline-danger btn-sm" style="margin:4px;" name="removeQuestion" value=<?= htmlspecialchars($questionID); ?>>delete</button>
            </form>
        <?php endif;?>
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
                                <form action="./user-questions.php" method="get">
                                    <button type="submit" class="btn btn-info text-white btn-sm" name="userID" value=<?= htmlspecialchars($questionComment['userID']); ?>><?= htmlspecialchars($accountManager->getUsername($questionComment['userID'])); ?></button>
                                </form>

                                <p><?= htmlspecialchars($questionComment['comment']); ?></p>
                                <form action="" method="post" style="margin:-16px 0px 0px 0px;">
                                <?php if($userID == $questionComment['userID'] || $isAdmin):?>

                                        <button type="submit" class="btn btn-outline-danger btn-sm" name="removeQuestionComment" value=<?= htmlspecialchars($questionComment['id']); ?>>delete</button>
                                        <button type="submit" class="btn btn-outline-secondary btn-sm" name="editQuestionComment" value=<?= htmlspecialchars($questionComment['id']); ?>>edit</button>
                                <?php endif;?>
                                    <button type="submit" class="btn btn-outline-secondary btn-sm" name="replyComment" value=<?= htmlspecialchars($questionComment['id']); ?>>reply</button>
                                </form>

                                <?php $childComments = $commentManager->getParentsChildComments($questionComment['id']);
                                foreach ($childComments as &$childComment):?>
                                    <div style="margin-left: 32px;">
                                        <?= htmlspecialchars($childComment['comment']);?>
                                        <form action="" method="post">
                                        <?php if($userID == $childComment['userID'] || $isAdmin):?>
                                            <button type="submit" class="btn btn-outline-danger btn-sm" name="removeChildComment" value=<?= htmlspecialchars($childComment['id']); ?>>delete</button>
                                        <?php endif;?> 
                                        <?php if($userID == $childComment['userID']):?>
                                                <button type="submit" class="btn btn-outline-secondary btn-sm" name="editChildComment" value=<?= htmlspecialchars($childComment['id']); ?>>edit</button>
                                        <?php endif;?> 
                                        <input type="hidden" name="replyToUserID" value=<?= htmlspecialchars($childComment['userID']);?>>
                                        <button type="submit" class="btn btn-outline-secondary btn-sm" name="replyComment" value=<?= htmlspecialchars($questionComment['id']); ?>>reply</button>
                                        </form>
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

