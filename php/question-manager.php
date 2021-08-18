<?php
class QuestionManager{
    public $conn;

    public function __construct($conn){
        $this->conn = $conn;
    }

    public function __destruct()
    {
        $this->conn = null;
    }

    public function checkQuestion($question){
        if(empty($question)){
            return "Question can't be empty!";
        }
        else if(strlen($question)<10){
            return "Question must be at least 10 characters long!";
        }
        else if(strlen($question)>255){
            return "Question is too long!";
        }
        else{
            return "";
        }
    }

    private function formatQuestion($question){
        $question = ltrim($question);
        if(!preg_match('~^[\s\S]+[!?.]+$~u', $question)) {
            $question .= "?";
        }
        return $question;
    }

    public function saveQuestion($question,$userID){
        $question = $this->formatQuestion($question);

        $questionData = array(
            ':question' => $question,
            ':userID' => $userID
        );

        $sql = <<<EOSQL
            INSERT INTO Questions (question, userID) VALUES(:question, :userID);
        EOSQL;

        $stmt= $this->conn->prepare($sql);
        $stmt->execute($questionData);
    }

    public function getUserQuestionData($userID){
        $questionData = array();

        $sql = <<<EOSQL
            SELECT * FROM Questions WHERE userID = :userID;
        EOSQL;

        $questions = $this->conn->prepare($sql);
        $questions->execute([':userID'=>$userID]);
        while($question = $questions->fetch()){
            $sql = <<<EOSQL
                SELECT answer FROM Answers WHERE questionID = :questionID;
            EOSQL;

            $answers = $this->conn->prepare($sql);
            $answers->execute([':questionID'=>$question['id']]);
            $answers = $answers->fetchAll(PDO::FETCH_ASSOC);
            $answers = array_column($answers, 'answer');
            
            $answerData = array(
                'question' => $question['question'],
                'yes' => 0,
                'no' => 0
            );

            $answerValues = array_count_values($answers);
            if(array_key_exists("yes",$answerValues)){
                $answerData['yes'] = $answerValues['yes'];
            }
            if(array_key_exists("no",$answerValues)){
                $answerData['no'] = $answerValues['no'];
            }

            //echo 'Question:' . $answerData['question'] . '</br>Yes: ' . $answerData['yes'] . '</br>No: ' . $answerData['no'] . '</br>';
            array_push($questionData,$answerData);
        }
        return $questionData;
    }

    public function getPositivePercentage($yesAmount,$noAmount){
        if($noAmount==0){
            if($yesAmount==0){
                return 0;
            }
            else{
                return 100;
            }
        }
        else{
            $percentage = (float)($yesAmount/($yesAmount+$noAmount)) * 100;
            return $percentage;
        }
    }

    public function getNegativePercentage($yesAmount,$noAmount){
        $positivePercentage = $this->getPositivePercentage($yesAmount,$noAmount);

        if($positivePercentage==0){
            return (bool)$noAmount * 100;
        }
        else{
            return (float)(100 - $positivePercentage);
        }
    }


    public function getAvailableQuestions($userID){
        $sql = <<<EOSQL
            SELECT * FROM Questions WHERE id NOT IN (SELECT questionID FROM Answers where userID = :userID) AND id NOT IN (SELECT id FROM Questions where userID = :userID);
        EOSQL;

        $query = $this->conn->prepare($sql);
        $query->execute([':userID'=>$userID,':userID'=>$userID]);
        $query->setFetchMode(PDO::FETCH_ASSOC);
        return $query;
    }

    public function answerQuestion($questionID,$userID,$answer){
      $answer = array(
            ':questionID' => $questionID,
            ':userID' => $userID,
            ':answer' => $answer
        );

        $sql = <<<EOSQL
            INSERT INTO Answers (questionID, userID, answer) VALUES(:questionID, :userID, :answer);
        EOSQL;

        $stmt= $this->conn->prepare($sql);
        $stmt->execute($answer);
    }
}

?>