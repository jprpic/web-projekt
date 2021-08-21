<?php

class CommentManager{

    public $conn;

    public function __construct($conn){
        $this->conn = $conn;
    }

    public function __destruct(){
        $this->conn = null;
    }

    /*
    CREATE TABLE $tableName(
    id INT AUTO_INCREMENT PRIMARY KEY,
    questionID INT NOT NULL,
    userID INT NOT NULL,
    comment VARCHAR(500),
    FOREIGN KEY (questionID) REFERENCES Questions(id) ON DELETE CASCADE,
    FOREIGN KEY (userID) REFERENCES Users(id) ON DELETE CASCADE
    );*/

    public function answerQuestion($questionID,$userID,$comment){
        $questionComment = <<<EOSQL
            INSERT INTO Question_comments (questionID, userID, comment) VALUES (:questionID, :userID, :comment);
        EOSQL;

        $questionCommentEntry = $this->conn->prepare($questionComment);
        $questionCommentEntry->execute([':questionID'=>$questionID,':userID'=>$userID,':comment'=>$comment]);
    }

    public function loadQuestionComments($questionID){
        $sql = <<<EOSQL
            SELECT * FROM Question_comments WHERE questionID = :questionID;
        EOSQL;

        $comments = $this->conn->prepare($sql);
        $comments->execute([':questionID'=>$questionID]);
        $comments = $comments->fetchAll(PDO::FETCH_ASSOC);

        return $comments;
    }

    public function removeComment($commentID,$commentType){
        if($commentType=="question"){
            $tableName = "Question_comments";
        }
        else if($commentType=="reply"){
            $tableName = "Parent_child_comments";
        }
        $delete = $this->conn->prepare("DELETE FROM $tableName WHERE id = :commentID");
        $delete->execute([':commentID'=>$commentID]);
    }

    public function replyToComment($userID,$parent_comment_ID){
        $comment = "This is a reply!";

        $childCommentSQL = <<<EOSQL
            INSERT INTO Parent_child_comments (parent_comment_id, userID, comment) VALUES (:parent_comment_id, :userID, :comment);
        EOSQL;

        $childCommentEntry = $this->conn->prepare($childCommentSQL);
        $childCommentEntry->execute([':parent_comment_id'=>$parent_comment_ID,':userID'=>$userID,':comment'=>$comment]);
        
    }

    public function loadChildComments($parent_comment_ID){
        $sql = <<<EOSQL
            SELECT * FROM Parent_child_comments WHERE parent_comment_id = :parent_comment_id;
        EOSQL;

        $comments = $this->conn->prepare($sql);
        $comments->execute([':parent_comment_id'=>$parent_comment_ID]);
        $comments = $comments->fetchAll(PDO::FETCH_ASSOC);

        return $comments;
    }

    public function editComment($commentID,$commentType){
        $comment = "This is an edited comment!";

        if($commentType=="question"){
            $tableName = "Question_comments";
        }
        else if($commentType=="reply"){
            $tableName = "Parent_child_comments";
        }

        $sql = <<<EOSQL
                UPDATE $tableName
                SET comment = :comment
                WHERE id = :commentID
        EOSQL;

        $edit = $this->conn->prepare($sql);
        try{
            $edit->execute([':comment'=>$comment,':commentID'=>$commentID]);
        }
        catch(PDOException $e){
            echo $e->getMessage();
        }
        
    }

    public function countComments($userID){
        $sql = <<<EOSQL
            SELECT COUNT(*) FROM Question_comments WHERE userID = :userID;
        EOSQL;

        $qCount = $this->conn->prepare($sql);
        $qCount->execute([':userID'=>$userID]);
        $qCount = $qCount->fetch(PDO::FETCH_ASSOC);

        $sql = <<<EOSQL
            SELECT COUNT(*) FROM Parent_child_comments WHERE userID = :userID;
        EOSQL;

        $cCount = $this->conn->prepare($sql);
        $cCount->execute([':userID'=>$userID]);
        $cCount = $cCount->fetch(PDO::FETCH_ASSOC);

        return $cCount['COUNT(*)'] + $qCount['COUNT(*)'];
    }

}


/*
CREATE TABLE Comments(
    id INT AUTO_INCREMENT PRIMARY KEY,
    comment VARCHAR(500) NOT NULL,
    userID INT,
    questionID INT,
    FOREIGN KEY (userID) REFERENCES Users(id),
    FOREIGN KEY (questionID) REFERENCES Questions(id)
);
*/

/*
CREATE TABLE Parent_child_comment(
    parent_comment_id INT,
    child_comment_id INT,
    PRIMARY KEY (parent_comment_id,child_comment_id),
    FOREIGN KEY (parent_comment_id) REFERENCES Comments(id),
    FOREIGN KEY (child_comment_id) REFERENCES Comments(id)
);
*/
?>