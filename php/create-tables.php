<?php
if(isset($_POST['submit'])){
    header("Location:index.php");
}

require_once('./dbconfig.php');

try{
    $conn=DBConfig::getConnection();
    echo "Connected! </br>";
}catch(Exception $error){
    echo $error->getMessage();
    exit;
}

require_once('./dbtable.php');

$dbtable = new DBTable($conn);

// Users table creation

$tableName = "Users";
$tableSQL = <<<EOSQL
CREATE TABLE $tableName(
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    username VARCHAR(32) NOT NULL,
    password VARCHAR (255) NOT NULL
);
EOSQL;

$dbtable->createTable($tableName,$tableSQL);

// Questions table creation

$tableName = "Questions";
$tableSQL= <<<EOSQL
CREATE TABLE $tableName(
    id INT AUTO_INCREMENT PRIMARY KEY,
    question VARCHAR(255) NOT NULL,
    userID INT NOT NULL,
    FOREIGN KEY (userID) REFERENCES Users(id) ON DELETE CASCADE
);
EOSQL;

$dbtable->createTable($tableName,$tableSQL);

// Answers table creation

$tableName = "Answers";
$tableSQL= <<<EOSQL
CREATE TABLE $tableName(
    questionID INT ,
    userID INT,
    answer VARCHAR(3) NOT NULL,
    CHECK (answer = 'yes' OR answer = 'no'),
    PRIMARY KEY (questionID,userID),
    FOREIGN KEY (questionID) REFERENCES Questions(id) ON DELETE CASCADE,
    FOREIGN KEY (userID) REFERENCES Users(id) ON DELETE CASCADE
);
EOSQL;

$dbtable->createTable($tableName,$tableSQL);

// Question_comments table creation

$tableName = "Question_comments";
$tableSQL = <<<EOSQL
CREATE TABLE $tableName(
    id INT AUTO_INCREMENT PRIMARY KEY,
    questionID INT NOT NULL,
    userID INT NOT NULL,
    comment VARCHAR(500),
    FOREIGN KEY (questionID) REFERENCES Questions(id) ON DELETE CASCADE,
    FOREIGN KEY (userID) REFERENCES Users(id) ON DELETE CASCADE
);
EOSQL;

$dbtable->createTable($tableName,$tableSQL);

// Parent-Child-Comment table creation

$tableName = "Parent_child_comments";
$tableSQL = <<<EOSQL
CREATE TABLE $tableName(
    id INT AUTO_INCREMENT PRIMARY KEY,
    parent_comment_id INT NOT NULL,
    userID INT NOT NULL,
    comment VARCHAR(500),
    FOREIGN KEY (parent_comment_id) REFERENCES Question_comments(id) ON DELETE CASCADE,
    FOREIGN KEY (userID) REFERENCES Users(id) ON DELETE CASCADE
);
EOSQL;

$dbtable->createTable($tableName,$tableSQL);

// All tables created -- unset connection

unset($conn);
?>

<!DOCTYPE html>
<html>

<head>
    <title>Create Tables</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
</head>

<body>
<form action="" method="POST">
    <input type="submit" name="submit" value="Go to Questions" class="btn btn-primary text-white">
</form>

</body>

</html>
