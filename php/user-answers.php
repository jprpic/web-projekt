<?php
session_start();
if(!isset($_SESSION['userID'])){
    header('Location:./login.php');
}

require_once('./answer-manager.php');
require_once('./dbconfig.php');

$answerManager = new AnswerManager(DBConfig::getConnection());

$QnAData = $answerManager->getUserAnswers($_SESSION['userID']);
?>

<!DOCTYPE html>
<html>

    <head>
        <title>Your Questions</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    </head>

    <body>

        <table class="table table-striped text-center">
            <thead>
                <tr>
                    <th scope="col">Question</th>
                    <th scope="col">Answer</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($QnAData as &$QnA): ?>
                    <tr>
                        <td><?= htmlspecialchars($QnA['question']) ?></td>
                        <td>
                            <?php echo $QnA['answer'];?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
    </body>
</html>