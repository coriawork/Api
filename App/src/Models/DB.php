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
    public function ExistIn($table,$data){
       if($this->makeQuery('SELECT id FROM ' . $table . ' WHERE id = ' . $data . '')->fetch_array()==null)return false;
       return true;
    }
}
?>