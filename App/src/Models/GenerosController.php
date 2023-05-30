<?php
namespace App\src\Models;
use App\src\Models\DB;
use Exception;

class GenerosController{  
    //* todos los generos
    public function list($request, $response, $args)
    {
        $db = new DB();
        try{
            $generos = $db->makeQuery('SELECT * FROM generos')->fetch_all(MYSQLI_ASSOC);
            //*forma de enviar las exepciones
            if (sizeof($generos) === 0) {
                throw new Exception("No hay generos", 404);
            }
            $response->getBody()->write(json_encode($generos));
            return $response->withStatus(200);
        }
        catch(Exception $e){
            $response->getBody()->write($e->getMessage());
            return $response->withStatus(404);
        }
        //todo: catch pdo exception para la conexion de la base de datos
    }
    //*post 201 si se creo bien

    //* crea un genero
    public function create($request, $response, $args){
        $db = new DB();
        $nombre = json_decode($request->getBody(), true)['nombre'];
        $db->makeQuery("INSERT INTO generos (nombre) VALUES ('".$nombre."')");
        return $response->withStatus(200);
    }

    //* actualizar genero con id
    public function update($request, $response, $args){
        $db = new DB();
        try{
            $body = json_decode($request->getBody(), true);
            $result = $db->makeQuery("SELECT * from generos where id = '" . $body['id'] . "'");
            if ($result->num_rows === 0) throw new Exception("No existe el id", 400);
            if (!isset($body['id'])) throw new Exception("no se recibio el id a editar",400);
            if (!isset($body['nombre'])) throw new Exception("no se recibio el parametro",400);

            $result = $db->makeQuery("UPDATE generos SET nombre='" . $body['nombre'] . "' WHERE id='" . $body['id'] . "'");
            var_dump($result);
            return $response->withStatus(200);
        }
        catch(Exception $e){
            if($e->getCode() == 400){
                $response->getBody()->write($e->getMessage());
                return $response->withStatus(400);
            }
            if($e->getCode() == 1){
                $response->getBody()->write($e->getMessage());
                return $response->withStatus(404);
            }
            $response->getBody()->write($e->getMessage());
            return $response->withStatus(400);
        }
    }
    public function delete($request, $response, $args){
        $db = new DB();
        try{
            $body = json_decode($request->getBody(), true);
            $result = $db->makeQuery("SELECT * from generos where id = '" . $body['id'] . "'");
            if($result->num_rows === 0) throw new Exception("No existe el id", 400);
            if (!isset($body['id'])) throw new Exception("No se recibio el id", 400);
            $result = $db->makeQuery("DELETE FROM generos where id = '".$body['id']."'");
            var_dump($result);
            return $response;
        }
        catch(Exception $e){
            $response->getBody()->write($e->getMessage());
            return $response->withStatus(400);
        }
    }
}
?>    
