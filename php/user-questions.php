<?php
if(!isset($_COOKIE['user'])){
    header('Location:./login.php');
}

require_once('./dbconfig.php');
require_once('./question-manager.php');

$questionManager = new QuestionManager(DBConfig::getConnection());
$questionData = $questionManager->getUserQuestionData($_COOKIE['user']);

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
                    <th scope="col">Yes</th>
                    <th scope="col">No</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($questionData as &$question): ?>
                    <tr>
                        <td><?= htmlspecialchars($question['question']) ?></td>
                        <td>
                            <?php echo $question['yes'];
                            echo ' (' . round($questionManager->getPositivePercentage($question['yes'],$question['no'])) . '%)';?>
                        </td>
                        <td>
                        <?php echo $question['no'];
                            echo ' (' . round($questionManager->getNegativePercentage($question['yes'],$question['no'])) . '%)';?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
    </body>
</html>