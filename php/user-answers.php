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
require_once('./managers/account-manager.php');

$conn = DBConfig::getConnection();
$accountManager = new AccountManager($conn);
$userID = $_GET['userID'];
if(!$accountManager->exists($userID)){
    header('Location:./missing-data.php');
}

$answerManager = new AnswerManager($conn);
$ownerID = $_SESSION['userID'];

if(isset($_POST['remove'])){
    $answerManager->removeAnswer($_POST['remove'],$_GET['userID']);
}

require_once('./managers/question-manager.php');
require_once('./managers/comment-manager.php');

$questionManager = new QuestionManager($conn);
$accountManager = new AccountManager($conn);
$commentManager = new CommentManager($conn);
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

            <div class="boxWithQ" id="boxWithQ" >

                <div class="d-flex justify-content-center" style="margin:8px;">
                    <form action="./user-questions.php" method="get" style="margin:4px;">
                        <button type="submit" name="userID" value=<?= htmlspecialchars($userID);?> class="button_slide slide_left">Questions</button>
                    </form>
                    <form action="./user-answers.php" method="get" style="margin:4px;">
                        <button type="submit" name="userID" value=<?= htmlspecialchars($userID);?> class="button_slide slide_left active1">Answers</button>
                    </form>
                    <form action="./user-comments.php" method="get" style="margin:4px;">
                        <button type="submit" name="userID" value=<?= htmlspecialchars($userID);?> class="button_slide slide_left">Comments</button>
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
                                    <button type="submit" name="questionID" value=<?= htmlspecialchars($questionID); ?> class="buttonQuestion question1"><?= htmlspecialchars($questionManager->getQuestion($questionID)); ?></button>
                                </form>
                            </td>
                            <td>
                                <?= htmlspecialchars($answerManager->getUserAnswer($questionID,$userID));?>
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

    <footer>
    <p>Copyright ?? 2021</p>
    <p>D.Rojni??, J.Prpi??, D.Dra??eti??</p>
</footer>
</html>