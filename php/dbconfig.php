<?php

final class DBConfig
{
    const HOST = 'eu-cdbr-west-01.cleardb.com';
    const DB_NAME = 'heroku_dcd1db2779b550f';
    const USERNAME = 'be4d7787062ad3';
    const PASS = 'd6e08553';

    public static function getConnection(){
        try {
            $conn = new PDO(
                "mysql:host=" . DBConfig::HOST . ";dbname=" . DBConfig::DB_NAME,
                DBConfig::USERNAME,
                DBConfig::PASS
            );
        } catch (PDOException $e) {
            die("ERROR: Could not connect to the database. " . $e->getMessage());
        }
        return $conn;
    }
}
