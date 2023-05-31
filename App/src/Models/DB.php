<?php
namespace App\src\Models;
use PDO;
use PDOStatement;
class DB extends PDO{
    private $host = 'localhost';
    private $user = 'root';
    private $pass = '';
    private $db = 'pagjuego';
    private $con;
    
    public function __construct(){
        $dsn = "mysql:host=$this->host;dbname=$this->db;charset=utf8mb4";
        $options = [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            \PDO::ATTR_EMULATE_PREPARES => false,
        ];
        try {
            $this->con = new \PDO($dsn, $this->user, $this->pass, $options);
	}
       	catch (\PDOException $e) {
            throw new \PDOException($e->getMessage(), (int)$e->getCode());
        }
    }
    
    public function prepare($query, $options = []): \PDOStatement {
        return $this->con->prepare($query, $options);
    }

    public function execute(\PDOStatement $statement, array $parameters = []): bool {
        return $statement->execute($parameters);
    }

    public function makeQuery($query, $params = []){
        $stmt = $this->con->prepare($query);
        $stmt->execute($params);
        return $stmt;
    }
    
    public function close(){
        $this->con = null;
    }
    
    public function existsIn($table, $data){
        $query = 'SELECT id FROM ' . $table . ' WHERE id = :data';
        $stmt = $this->makeQuery($query, [':data' => $data]);
	return $stmt->fetch() !== false;
	}
}
?>
