<?php

namespace App\src\Models;
class DB{
    private $root = 'localhost';
    private $user = 'root';
    private $pass = '';
    private $db = 'pagjuego';
    private $con;
    public function __construct(){
        $this->con = mysqli_connect($this->root,$this->user,$this->pass,$this->db);
    }
    public function makeQuery($query){
        return $this->con->query($query);
    }
    public function close(){
        $this->con->close();
    }
}
?>