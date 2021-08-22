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
$userID = $_SESSION['userID'];

if(isset($_POST['yesanswer'])){
    $questionID = $_POST['yesanswer'];
    if($answerManager->getUserAnswer($questionID,$userID)){
        $answerManager->changeAnswer($questionID,$userID,"yes");
    }
    else{
        $answerManager->answerQuestion($questionID,$userID,"yes");
    }
    $location = 'Location:./question.php?questionID=' . $questionID;
    header($location);
}

if(isset($_POST['noanswer'])){
    $questionID = $_POST['noanswer'];
    if($answerManager->getUserAnswer($questionID,$userID)){
        $answerManager->changeAnswer($questionID,$userID,"no");
    }
    else{
        $answerManager->answerQuestion($questionID,$userID,"no");
    }
    $location = 'Location:./question.php?questionID=' . $questionID;
    header($location);
}

require_once('./managers/question-manager.php');
$questionManager = new QuestionManager($conn);
$availableQuestions = $questionManager->getAvailableQuestions();

unset($conn);
?>


<!DOCTYPE html>
<html>

<head>
    <title>Home Page</title>
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

    <section>
    <table class="table table-striped text-center">
        <thead>
                <tr>
                    <th scope="col">Question</th>
                    <th scope="col">Yes</th>
                    <th scope="col">No</th>
                </tr>
            </thead>
        <tbody>
            <?php while ($question = $availableQuestions->fetch()) : ?>
                <?php $questionAnswer = $answerManager->getUserAnswer($question['id'],$userID)?>
                <tr>
                    <td>
                        <form action="question.php" method="get">
                            <button type="submit" name="questionID" value=<?= $question['id'] ?> class="btn btn-danger text-white"><?= htmlspecialchars($questionManager->getQuestion($question['id'])) ?></button>
                        </form>
                    </td>
                    <?php $isQuestionOwner = $questionManager->getOwner($question['id']) == $userID?>
                    <td>
                        <form action="" method="POST">
                            <button type="submit" name="yesanswer" value=<?=$question['id']?> <?php if($isQuestionOwner || $questionAnswer=="yes"){echo "disabled";}?> class="btn btn-primary text-white">Yes</button>
                        </form>
                    </td>
                    <td>
                        <form action="" method="POST">
                            <button type="submit" name="noanswer" value=<?=$question['id']?> <?php if($isQuestionOwner || $questionAnswer=="no"){echo "disabled";}?> class="btn btn-danger text-white">No</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    </section>
    
</body>
</html>