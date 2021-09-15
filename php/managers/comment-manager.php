<?php

class CommentManager{

    public $conn;
    const TYPE_COMMENT= 'comment';
    const TYPE_REPLY = 'reply';

    public function __construct($conn){
        $this->conn = $conn;
    }

    public function __destruct(){
        $this->conn = null;
    }


    public function answerQuestion($questionID,$userID,$comment){
        $questionComment = <<<EOSQL
            INSERT INTO Question_comments (questionID, userID, comment) VALUES (:questionID, :userID, :comment);
        EOSQL;

        $questionCommentEntry = $this->conn->prepare($questionComment);
        $questionCommentEntry->execute([':questionID'=>$questionID,':userID'=>$userID,':comment'=>$comment]);
    }

    public function getQuestionComments($questionID){
        $sql = <<<EOSQL
            SELECT * FROM Question_comments WHERE questionID = :questionID ORDER BY creationTime DESC;
        EOSQL;

        $comments = $this->conn->prepare($sql);
        $comments->execute([':questionID'=>$questionID]);
        $comments = $comments->fetchAll(PDO::FETCH_ASSOC);

        return $comments;
    }

    public function removeComment($commentID,$commentType){
        if($commentType==self::TYPE_COMMENT){
            $tableName = "Question_comments";
        }
        else if($commentType==self::TYPE_REPLY){
            $tableName = "Child_comments";
        }
        $delete = $this->conn->prepare("DELETE FROM $tableName WHERE id = :commentID");
        $delete->execute([':commentID'=>$commentID]);
    }

    public function replyToComment($userID,$parentID,$comment){
        $childCommentSQL = <<<EOSQL
            INSERT INTO Child_comments (parentID, userID, comment) VALUES (:parentID, :userID, :comment);
        EOSQL;

        $childCommentEntry = $this->conn->prepare($childCommentSQL);
        $childCommentEntry->execute([':parentID'=>$parentID,':userID'=>$userID,':comment'=>$comment]);
    }

    public function getParentsChildComments($parentID){
        $sql = <<<EOSQL
            SELECT * FROM Child_comments WHERE parentID = :parentID;
        EOSQL;

        $comments = $this->conn->prepare($sql);
        $comments->execute([':parentID'=>$parentID]);
        $comments = $comments->fetchAll(PDO::FETCH_ASSOC);

        return $comments;
    }

    public function countComments($userID){
        $sql = <<<EOSQL
            SELECT COUNT(*) FROM Question_comments WHERE userID = :userID;
        EOSQL;

        $qCount = $this->conn->prepare($sql);
        $qCount->execute([':userID'=>$userID]);
        $qCount = $qCount->fetch(PDO::FETCH_ASSOC);

        $sql = <<<EOSQL
            SELECT COUNT(*) FROM Child_comments WHERE userID = :userID;
        EOSQL;

        $cCount = $this->conn->prepare($sql);
        $cCount->execute([':userID'=>$userID]);
        $cCount = $cCount->fetch(PDO::FETCH_ASSOC);

        return $cCount['COUNT(*)'] + $qCount['COUNT(*)'];
    }

    public function getUserQuestionComments($userID){
        $sql = <<<EOSQL
            SELECT * FROM Question_comments WHERE userID = :userID;
        EOSQL;

        $questionComments = $this->conn->prepare($sql);
        $questionComments->execute([':userID'=>$userID]);
        $questionComments->setFetchMode(PDO::FETCH_ASSOC);
        $questionComments = $questionComments->fetchAll();

        return $questionComments;
    }

    public function getUserChildComments($userID){
        $sql = <<<EOSQL
            SELECT * FROM Child_comments WHERE userID = :userID;
        EOSQL;

        $childComments = $this->conn->prepare($sql);
        $childComments->execute([':userID'=>$userID]);
        $childComments->setFetchMode(PDO::FETCH_ASSOC);
        $childComments = $childComments->fetchAll();

        return $childComments;
    }

    public function getQuestionID($parentID){
        $sql = <<<EOSQL
            SELECT questionID FROM Question_comments where id = :parentID;
        EOSQL;

        $question = $this->conn->prepare($sql);
        $question->execute([':parentID'=>$parentID]);
        $question->setFetchMode(PDO::FETCH_ASSOC);
        $question = $question->fetch();

        return $question['questionID'];
    }

}

?>