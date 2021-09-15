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

if(isset($_POST['commentSubmit']) && $_POST['commentText']){
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

if(isset($_POST['replyComment']) && $_POST['replyToUser']){
    $comment =  $_POST['replyToUser'];
    if(isset($_POST['replyToUserID'])){
        $comment = '@' . $accountManager->getUsername($_POST['replyToUserID']) . ' ' . $comment;
    }
    $commentManager->replyToComment($userID,$_POST['replyComment'],$comment);
}

$questionComments = $commentManager->getQuestionComments($questionID);


$questionOwnerID = $questionManager->getOwner($questionID); 
$questionOwner = $accountManager->getUsername($questionOwnerID);

$userName = $accountManager->getUsername($userID);


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
                    <button type="submit" class="button_slide slide_left active1"><span class="text">Home</span></button>
                </form>
            </li>
            <li>
                <form action="./create-question.php" method="post" >
                    <button type="submit" class="button_slide slide_left"><span class="text">Create a Question</span></button>
                </form>
            </li>
            <li>
                <form action="./user-questions.php" method="get" >
                    <button type="submit" name="userID" value="<?= htmlspecialchars($_SESSION['userID']);?>" class="button_slide slide_left"><span class="text">your profile</span></button>
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
    <p class="username">Hi, <?= htmlspecialchars($userName);?>!</p>
</div>

<body>

    <div class="text-center" style="padding: 100px 100px 40px 100px; font-size: 1.3em;">
        <div class="text-danger1"> <?= htmlspecialchars($question); ?></div>
        <?php if($userAnswer):?>
            <p style="background-color:#E6E6E6;">You voted <span class=<?php if($userAnswer=="no"){echo "text-danger";}else{echo "text-success";}?>><?= htmlspecialchars($userAnswer); ?></span></p>
        <?php endif;?>
    </div>
    <div class="d-flex justify-content-center">
        <div class="text-left bg-danger text-white" style="padding: 64px;font-size: 32px;margin:32px;">
            <?= htmlspecialchars($answerCount['no']); ?>
        </div>
        <div class="text-right bg-success text-white" style="padding: 64px;font-size: 32px;margin:32px;">
            <?= htmlspecialchars($answerCount['yes']); ?>
        </div>
    </div>


    <div class="d-flex justify-content-center" style="padding-bottom:50px;">
        <form action="./question.php" method="get">
            <button type="submit" name="questionID" style="margin:0px 16px;" class="btn btn-info btn-sm" <?php
            $previousQuestionID = htmlspecialchars($questionManager->getPreviousQuestion($userID,$questionID));
            if($previousQuestionID){echo "value=$previousQuestionID";}
            else{echo "disabled";} ?>><<</button>
        </form>
        <form action="" method="post">
                <button type="submit" name="noanswer" style="margin:0px 16px;"value=<?= htmlspecialchars($questionID);?> class="answer1 red"
                <?php if($userAnswer=="no" || $isQuestionOwner){echo "disabled";}?>>No</button>
        </form>
       
        <form action="" method="post">
            <button type="submit" name="yesanswer" style="margin:0px 16px;"value=<?= htmlspecialchars($questionID); ?> class="answer1"
            <?php if($userAnswer=="yes" || $isQuestionOwner){echo "disabled";}?>>Yes</button>
        </form>
        <form action="./question.php" method="get">
            <button type="submit" name="questionID"  style="margin:0px 16px;" class="btn btn-info btn-sm"<?php
            $nextQuestionID = htmlspecialchars($questionManager->getNextQuestion($userID,$questionID));
            if($nextQuestionID){echo "value=$nextQuestionID";}
            else{echo "disabled";} ?>>>></button>
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

    <!--  KOMENTAR -->
    <section class=" p-2 text-left bg-light">
        <form method="POST" action="">
            <div style="text-align: center;" ><label  for="email">Enter your comment here:</label></div>
        </br>
            <div class="commentbox" style="max-width: 600px; margin: 0px auto; ">

                <textarea id="commentText" class="text-danger1" name="commentText" rows="2"    style="width:100%;   "></textarea>
            </div>
           <div style="text-align: center;" > <input type="submit" id="commentSubmit" name="commentSubmit" value ="Submit" class="button_slide slide_left" style="margin-top:20px; "> </div></br>
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
                                <div style="padding-top:10px;"></div>
                                <?php if($userID == $questionComment['userID'] || $isAdmin):?>
                                   
                                        <button type="submit" class="btn btn-outline-danger btn-sm" name="removeQuestionComment" value=<?= htmlspecialchars($questionComment['id']); ?>>delete</button>
                                        <?php endif;?>
                                
                                <input type="hidden" name="replyToUserID" value=<?= htmlspecialchars($questionComment['userID']);?>>
                                    <button type="submit" class="btn btn-outline-secondary btn-sm" name="replyComment" value=<?= htmlspecialchars($questionComment['id']); ?>>reply</button>
                                    <div class="commentbox" style="max-width: 600px; margin: 5px; ">
                                            <textarea style=" width:100%; " class="text-danger1" rows="1"  name="replyToUser"  placeholder="Write your reply here." ></textarea>
                                        </div>
                                </form>

                                <?php $childComments = $commentManager->getParentsChildComments($questionComment['id']);
                                foreach ($childComments as &$childComment):?>
                                 
                                 <div style="margin-left: 32px; padding-bottom: 10px; padding-left: 10px; border-bottom: 2px solid rgb(230, 230, 230); border-left: 2px solid rgb(230, 230, 230);">
                                 <form action="./user-questions.php" method="get">
                                    <button style="margin-top: 12px;" type="submit" class="btn btn-info text-white btn-sm" name="userID" value=<?= htmlspecialchars($questionComment['userID']); ?>><?= htmlspecialchars($accountManager->getUsername($childComment['userID'])); ?></button>
                                 </form>   
                                        <?= htmlspecialchars($childComment['comment']);?>
                                        <div style="margin-top:10px;"></div>
                                
                                        <form action="" method="post">
                                        <?php if($userID == $childComment['userID'] || $isAdmin):?>
                                            <button  type="submit" class="btn btn-outline-danger btn-sm" name="removeChildComment" value=<?= htmlspecialchars($childComment['id']); ?>>delete</button>
                                        <?php endif;?>
                                        <button type="submit" class="btn btn-outline-secondary btn-sm" name="replyComment" value=<?= htmlspecialchars($questionComment['id']); ?>>reply</button>
                                        <input type="hidden" name="replyToUserID" value=<?= htmlspecialchars($childComment['userID']);?>>
                                        <div class="commentbox" style="max-width: 600px; margin: 5px; ">
                                            <textarea style=" width:100%; " class="text-danger1" rows="1"  name="replyToUser"  placeholder="Write your reply here." ></textarea>
                                        </div>
                                        
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

<footer>
    <p>Copyright © 2021</p>
    <p>D.Rojnić, J.Prpić, D.Dražetić</p>
</footer>

</html>

