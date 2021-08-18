<?php
class AnswerManager{
    public $conn;

    public function __construct($conn){
        $this->conn = $conn;
    }

    public function __destruct(){
        $this->conn = null;
    }

    public function getUserAnswers($userID){
        $QnAData = array();

        $sql = <<<EOSQL
            SELECT * FROM Answers WHERE userID = :userID;
        EOSQL;

        $answers = $this->conn->prepare($sql);
        $answers->execute([':userID'=>$userID]);

        while($answer = $answers->fetch(PDO::FETCH_ASSOC)){
            $sql = <<<EOSQL
                SELECT question from Questions WHERE id = :questionID;
            EOSQL;

            $question = $this->conn->prepare($sql);
            $question->execute([':questionID'=>$answer['questionID']]);
            $question = $question->fetch(PDO::FETCH_ASSOC);
            
            $QnA = array(
                'question' => $question['question'],
                'answer' => $answer['answer']
            );
            array_push($QnAData,$QnA);
        }

        return $QnAData;
    }
}

?>