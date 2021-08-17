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

    public function formatQuestion($question){
        $question = ltrim($question);
        if(!preg_match('~^[\s\S]+[!?.]+$~u', $question)) {
            $question .= "?";
        }
        return $question;
    }

    public function saveQuestion($question,$userID){
        $questionData = array(
            ':question' => $question,
            ':userID' => $userID,
            ':yes' => 0,
            ':no' => 0
        );

        $sql = <<<EOSQL
            INSERT INTO Questions (question, yes, no, userID) VALUES(:question, :yes, :no, :userID);
        EOSQL;

        $stmt= $this->conn->prepare($sql);
        $stmt->execute($questionData);
    }

    public function getQuestions($userID){
        $sql = <<<EOSQL
            SELECT question, yes, no FROM Questions WHERE userID = :userID;
        EOSQL;

        $query = $this->conn->prepare($sql);

        try {
            $query->execute([':userID'=>$userID]);
            $query->setFetchMode(PDO::FETCH_ASSOC);
            return $query;
        } catch (Exception $e) {
            echo $e->getMessage();
        }
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
            $percentage = (float)((int)$yesAmount/((int)$yesAmount/(int)$noAmount));
            return $percentage;
        }
    }

    public function getNegativePercentage($yesAmount,$noAmount){
        $positivePercentage = $this->getPositivePercentage($yesAmount,$noAmount);

        if($positivePercentage==0){
            return 0;
        }
        else{
            return 100 - $positivePercentage;
        }
    }
}

?>