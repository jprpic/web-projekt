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

    public function getUserQuestionIDs($userID){
            $sql = <<<EOSQL
            SELECT id from Questions WHERE userID = :userID;
        EOSQL;

        $questionIDs = $this->conn->prepare($sql);
        $questionIDs->execute(['userID'=>$userID]);
        $questionIDs = $questionIDs->fetchAll(PDO::FETCH_ASSOC);
        $questionIDs = array_column($questionIDs,'id');

        return $questionIDs;
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
            return (bool)$yesAmount * 100;
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

    public function getAvailableQuestions(){
        $sql = <<<EOSQL
            SELECT * FROM Questions ORDER BY creationTime DESC;
        EOSQL;

        $questions = $this->conn->prepare($sql);
        $questions->execute();
        $questions->setFetchMode(PDO::FETCH_ASSOC);
        return $questions;
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

    public function getOwner($questionID){
        $sql = <<<EOSQL
            SELECT userID from Questions where id = :questionID
        EOSQL;

        $question = $this->conn->prepare($sql);
        $question->execute([':questionID' => $questionID]);
        $question = $question->fetch(PDO::FETCH_ASSOC);

        return $question['userID'];
    }

    public function countQuestions($userID){
        $questionCount = $this->conn->prepare("SELECT COUNT(question) FROM Questions where userID = :userID");
        $questionCount->execute([':userID'=>$userID]);
        $questionCount = $questionCount->fetch(PDO::FETCH_ASSOC);
        return $questionCount['COUNT(question)'];
    }
    
    public function removeQuestion($questionID){
        $sql = <<<EOSQL
            DELETE FROM Answers WHERE questionID = :questionID;
        EOSQL;

        $removeAnswers = $this->conn->prepare($sql);
        $removeAnswers->execute([':questionID'=>$questionID]);

        $sql = <<<EOSQL
            DELETE FROM Questions WHERE id = :questionID;
        EOSQL;

        $removeQuestion = $this->conn->prepare($sql);
        $removeQuestion->execute([':questionID'=>$questionID]);
    }

    public function exists($questionID){
        $questionExists = $this->conn->prepare("SELECT id FROM Questions where id = :questionID");
        $questionExists->execute([':questionID'=>$questionID]);
        $questionExists = $questionExists->fetch(PDO::FETCH_ASSOC);
        return (bool)$questionExists;
    }

    public function getPreviousQuestion($userID,$currentQuestionID){
        $questions = $this->getAvailableQuestions($userID);
        $nextQuestionID = null;
        while($question = $questions->fetch()){
            if($question['id'] > $currentQuestionID){
                $nextQuestionID = $question['id'];
            }
            else{
                return $nextQuestionID;
            }
        }
        return $nextQuestionID;
    }
    
    public function getNextQuestion($userID,$currentQuestionID){
        $questions = $this->getAvailableQuestions($userID);
        $nextQuestionID = null;
        while($question = $questions->fetch()){
            $nextQuestionID = $question['id'];
            if($question){
                if($question['id'] < $currentQuestionID){
                    return $nextQuestionID;
                }
            }
            else{
                return null;
            }
            
        }
    }
}

?>