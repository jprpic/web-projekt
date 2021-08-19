<?php
class AnswerManager{
    public $conn;

    public function __construct($conn){
        $this->conn = $conn;
    }

    public function __destruct(){
        $this->conn = null;
    }
    
    public function getUserAnswer($questionID,$userID){
        $sql = <<<EOSQL
            SELECT answer FROM Answers WHERE questionID = :questionID AND userID = :userID
        EOSQL;
        $answer = $this->conn->prepare($sql);
        $answer->execute([':questionID' => $questionID, ':userID' => $userID]);
        $answer = $answer->fetch(PDO::FETCH_ASSOC);
        if($answer){
            return $answer['answer'];
        }
        return null;
        
    }

    public function getAnsweredQuestionIDs($userID){
        $sql = <<<EOSQL
            SELECT questionID from Answers WHERE userID = :userID
        EOSQL;

        $questionIDs = $this->conn->prepare($sql);
        $questionIDs->execute([':userID' => $userID]);
        $questionIDs = $questionIDs->fetchAll(PDO::FETCH_ASSOC);
        $questionIDs = array_column($questionIDs,'questionID');

        return $questionIDs;
    }

    private function getQuestionAnswers($questionID){
        $sql = <<<EOSQL
                SELECT answer FROM Answers WHERE questionID = :questionID;
            EOSQL;

        $answers = $this->conn->prepare($sql);
        $answers->execute([':questionID'=>$questionID]);
        $answers = $answers->fetchAll(PDO::FETCH_ASSOC);

        $answers = array_column($answers, 'answer');
        $answerValues = array_count_values($answers);

        return $answerValues;
    }

    public function countAnswers($questionID){
        $answerCount = $this->getQuestionAnswers($questionID);

        if(!array_key_exists("yes",$answerCount)){
            $answerCount['yes'] = 0;
        }
        if(!array_key_exists("no",$answerCount)){
            $answerCount['no'] = 0;
        }

        return $answerCount;
    }

    public function countUserAnswers($userID){
        $answerCount = $this->conn->prepare("SELECT COUNT(answer) FROM Answers WHERE userID = :userID");
        $answerCount->execute([':userID'=>$userID]);
        $answerCount = $answerCount->fetch(PDO::FETCH_ASSOC);

        return $answerCount['COUNT(answer)'];
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

    public function changeAnswer($questionID,$userID,$answer){
        $answer = array(
            ':questionID' => $questionID,
            ':userID' => $userID,
            ':answer' => $answer
        );

        $sql = <<<EOSQL
            UPDATE Answers
            SET answer = :answer
            WHERE questionID = :questionID AND userID = :userID;
        EOSQL;

        $stmt= $this->conn->prepare($sql);
        $stmt->execute($answer);
    }

    public function removeAnswer($questionID,$userID){
        $sql = <<<EOSQL
            DELETE FROM Answers WHERE questionID = :questionID AND userID = :userID;
        EOSQL;

        $removeAnswer = $this->conn->prepare($sql);
        $removeAnswer->execute([':questionID'=>$questionID, ':userID'=>$userID]);
    }
}

?>