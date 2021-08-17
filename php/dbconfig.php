<?php

final class DBConfig
{
    const HOST = 'localhost';
    const DB_NAME = 'askme';
    const USERNAME = 'user';
    const PASS = 'user';

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
