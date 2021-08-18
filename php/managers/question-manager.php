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
        require_once('./managers/answer-manager.php');
        $answerManager = new AnswerManager($this->conn);
        $questionData = array();

        $sql = <<<EOSQL
            SELECT id,question FROM Questions WHERE userID = :userID;
        EOSQL;

        $questions = $this->conn->prepare($sql);
        $questions->execute([':userID'=>$userID]);

        while($question = $questions->fetch()){
            $answerData = $answerManager->countAnswers($question['id']);
            $answerData['question'] = $question['question'];
            
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

    public function getQuestion($questionID){
        $sql = <<<EOSQL
            SELECT question from Questions where id = :questionID
        EOSQL;

        $question = $this->conn->prepare($sql);
        $question->execute([':questionID' => $questionID]);
        $question = $question->fetch(PDO::FETCH_ASSOC);

        return $question['question'];
    }
}

?>