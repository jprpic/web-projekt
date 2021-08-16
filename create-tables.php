<?php

require_once('./dbconfig.php');

try{
    $conn=DBConfig::getConnection();
    echo "Connected!";
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
    name VARCHAR(32) NOT NULL,
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
    yes INT NOT NULL,
    no INT NOT NULL,
    userID INT NOT NULL,
    FOREIGN KEY (userID) REFERENCES Users(id)
);
EOSQL;

$dbtable->createTable($tableName,$tableSQL);


?>