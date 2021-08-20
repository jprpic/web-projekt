<?php

class CommentManager{

    public $conn;

    public function __construct($conn){
        $this->conn = $conn;
    }

    public function __destruct(){
        $this->conn = null;
    }

    public function answerQuestion($questionID,$userID,$comment){
        $commentID = $this->addComment($userID,$comment);

        $linkQuestionComment = <<<EOSQL
            INSERT INTO Question_comments (questionID, commentID) VALUES (:questionID, :commentID);
        EOSQL;

        $questionCommentEntry = $this->conn->prepare($linkQuestionComment);
        $questionCommentEntry->execute([':questionID'=>$questionID,':commentID'=>$commentID]);
    }

    public function loadQuestionComments($questionID){
        $sql = <<<EOSQL
            SELECT * FROM Comments WHERE id IN (SELECT commentID FROM Question_comments WHERE questionID = :questionID);
        EOSQL;

        $comments = $this->conn->prepare($sql);
        $comments->execute([':questionID'=>$questionID]);
        $comments = $comments->fetchAll(PDO::FETCH_ASSOC);

        return $comments;
    }

    public function removeComment($commentID){
        $delete = $this->conn->prepare("DELETE FROM Comments WHERE id = :commentID");
        $delete->execute([':commentID'=>$commentID]);
    }

    public function replyToComment($userID,$parent_comment_ID){
        $comment = "This is a reply!";
        $child_comment_ID = $this->addComment($userID,$comment);

        $linkReplyComment = <<<EOSQL
            INSERT INTO Parent_child_comments (parent_comment_id, child_comment_id) VALUES (:parent_comment_id, :child_comment_id);
        EOSQL;

        $questionCommentEntry = $this->conn->prepare($linkReplyComment);
        $questionCommentEntry->execute([':parent_comment_id'=>$parent_comment_ID,':child_comment_id'=>$child_comment_ID]);
        
    }

    private function addComment($userID,$comment){
        $createComment = <<<EOSQL
            INSERT INTO Comments (comment, userID) VALUES(:comment,:userID);
        EOSQL;

        $commentEntry = $this->conn->prepare($createComment);
        $commentEntry->execute([':comment'=>$comment,':userID'=>$userID]);
        $commentID = $this->conn->lastInsertId();

        return $commentID;
    }

    public function loadChildComments($parent_comment_ID){
        $sql = <<<EOSQL
            SELECT * FROM Comments WHERE id IN (SELECT child_comment_id FROM Parent_child_comments WHERE parent_comment_id = :parent_comment_id);
        EOSQL;

        $comments = $this->conn->prepare($sql);
        $comments->execute([':parent_comment_id'=>$parent_comment_ID]);
        $comments = $comments->fetchAll(PDO::FETCH_ASSOC);

        return $comments;
    }

    public function editComment($commentID){
        $comment = "This is an edited comment!";

        $sql = <<<EOSQL
            UPDATE Comments
            SET comment = :comment
            WHERE id = :commentID;
        EOSQL;

        $edit = $this->conn->prepare($sql);
        $edit->execute([':comment'=>$comment,':commentID'=>$commentID]);
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