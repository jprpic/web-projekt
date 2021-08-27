<?php
class DBTable
{
    public $conn;

    public function __construct($conn)
    {
        $this->conn=$conn;
    }

    public function __destruct()
    {
        // close the database connection
        $this->conn = null;
    }

    public function createTable($tableName,$tableSql)
    {
        $sql = $this->conn->prepare("DESCRIBE $tableName");

        try{
            $sql->execute();
            echo "$tableName table already exists! </br>";
            return;
        }
        catch(PDOException $e){
            $query = $this->conn->prepare($tableSql);
            try{
                $query->execute();
                echo "$tableName table created ! </br>";
            }
            catch(PDOException $e){
                echo $e->getMessage();
            }
        }
    }
}
?>