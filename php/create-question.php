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

$error='';

if(isset($_POST['submit'])){
    require_once('./managers/question-manager.php');
    require_once('./dbconfig.php');
    $questionManager = new QuestionManager(DBConfig::getConnection());
    $question = $_POST['question'];
    $error = $questionManager->checkQuestion($question);

    if(!$error){
        $questionManager->saveQuestion($question,$_SESSION['userID']);
        header('location:index.php');
    }
}
?>


<!DOCTYPE html>
<html>

    <head>
        <title>Create a Question</title>
        <link rel="stylesheet" href="styles.css">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    </head>

    <div class="header">
  <h1 class="logo">AskMe</h1>
  <input type="checkbox" id="nav-toggle" class="nav-toggle">
  <nav class="nav">
    <ul>
      <li>  
            <form action="./index.php" method="get" >
                <button type="submit" name="userID"  class="button_slide slide_left"><span class="text">Home</span></button>
            </form>
        </li>
      <li>  
            <form action="./create-question.php" method="get" >
                <button type="submit" name="userID"  class="button_slide slide_left active1"><span class="text">Create a Question</span></button>
            </form>
        </li>
      <li>  
            <form action="./user-questions.php" method="get" >
                <button type="submit" name="userID" value=<?= htmlspecialchars($_SESSION['userID']);?> class="button_slide slide_left"><span class="text">your profile</span></button>
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
</div>

    <body>
       

        <section style= "padding-top:100px;" class="container text-center bg-light">
            <h4 class="title">Create a Question</h4>
            <form class="" method="POST" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
                <label for="email">Question:</label></br>
                <div id="questionbox" style="max-width: 700px; margin: 0px auto; ">
                    <textarea id="question" name="question" rows="3" cols="100" style="padding:4px 0px 0px 8px; width:100%;"></textarea>
                </div>
                <div class="text-danger"> <?= htmlspecialchars($error) ?> </div>

                <input type="submit" id="submit" name="submit" value ="Submit" class="btn btn-primary text-white" style="margin-top:20px;"></br>
            </form>
        </section>

    </body>
</html>