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
$answerManager = new AnswerManager($conn);
$accountManager = new AccountManager($conn);
$userID = $_SESSION['userID'];
$userName = $accountManager->getUsername($userID);

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
    <link rel="stylesheet" href="styles.css">
    <meta name="viewport" content="width=device-width,initial-scale=1">
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




<body >


    <section style= "padding-top:100px;">
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
                            <button type="submit" name="questionID" value=<?= htmlspecialchars($question['id']); ?> class="buttonQuestion"><p class="question1"><?= htmlspecialchars($questionManager->getQuestion($question['id'])) ?></p></button>
                        </form>
                    </td>
                    <?php $isQuestionOwner = $questionManager->getOwner($question['id']) == $userID?>
                    <td>
                        <form action="" method="POST">
                            <button type="submit" name="yesanswer" value=<?= htmlspecialchars($question['id']); ?> <?php if($isQuestionOwner || $questionAnswer=="yes"){echo "disabled" ;}?> class="answer1">Yes</button>
                        </form>
                    </td>
                    <td>
                        <form action="" method="POST">
                            <button type="submit" name="noanswer" value=<?= htmlspecialchars($question['id']); ?> <?php if($isQuestionOwner || $questionAnswer=="no"){echo "disabled";}?> class="answer1 red">No</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    </section>

    
</body>
<footer>
    <p>Copyright © 2021</p>
    <p>D.Rojnić, J.Prpić, D.Dražetić</p>
</footer>

</html>