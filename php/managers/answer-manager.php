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

        return $answer['answer'];
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
}

?>