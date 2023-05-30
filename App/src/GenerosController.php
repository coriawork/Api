<?php
namespace App\src;
use App\src\DB;
use Exception;

class GenerosController{  
    
    private $db;
    public function __construct(){
        $this->db = new DB();
    }
    //devuelve todos los generos
    public function list($request, $response, $args)
    {
        try{
        $db = new DB();
        $generos = $db->makeQuery('SELECT * FROM generos')->fetch_all(MYSQLI_ASSOC);
        //forma de enviar las exepciones
        if (sizeof($generos) === 0) {
            throw new Exception("No hay generos", 1);
        }
        $response->getBody()->write(json_encode($generos));
        return $response->withStatus(200);}
        catch(Exception $e){
            $response->getBody()->write($e->getMessage());
            return $response->withStatus(400);
        }
        //catch pdo exception para la conexion de la base de datos
    }
    //post 201 si se creo bien

    //crea un nuevo genero
    public function create($request, $response, $args){
        $db = new DB();
        $nombre = json_decode($request->getBody(), true)['nombre'];
        $db->makeQuery("INSERT INTO generos (nombre) VALUES ('".$nombre."')");
        return $response->whitStatus(200);

    }
    //trae un s
    public function getGen($request, $response, $args){
        
    }

}
?>    
