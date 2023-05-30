<?php
namespace App\src\Models;
use App\src\Models\DB;
use Exception;

class GenerosController{
    //*post 201 si se creo bien

    //* crea un genero (a)
    public function create($request, $response, $args){
        $db = new DB();
        $nombre = json_decode($request->getBody(), true)['nombre'];
        $db->makeQuery("INSERT INTO generos (nombre) VALUES ('" . $nombre . "')");
        return $response->withStatus(200);
    }

    //* actualizar genero con id (b)
    public function update($request, $response, $args){
        $db = new DB();
        try{
            if (!is_numeric($args['id'])) throw new Exception("el id debe ser numerico", 400);
            if (!isset($args['id'])) throw new Exception("no se recibio el id para hacer el uptdate", 400);
            if (!$db->ExistIn('Generos', $args['id'])) throw new Exception("No se encontro el id: '" . $args['id'] . "'", 404);

            $body = json_decode($request->getBody(), true);
            if(!isset($body['nombre']))throw new Exception("no se recibio el parametro para update", 400);
            return $response->withStatus(200);
        }
        catch(Exception $e){
            $response->getBody()->write($e->getMessage());
            return $response->withStatus($e->getCode());
        }
    }
    //*delete (c)
    public function delete($request, $response, $args){
        $db = new DB();
        try{
            $body = json_decode($request->getBody(), true);
            $result = $db->makeQuery("SELECT * from generos where id = '" . $body['id'] . "'");
            if($result->num_rows === 0) throw new Exception("No existe el id", 400);
            if (!isset($body['id'])) throw new Exception("No se recibio el id", 400);
            $result = $db->makeQuery("DELETE FROM generos where id = '".$body['id']."'");
            return $response;
        }
        catch(Exception $e){
            $response->getBody()->write($e->getMessage());
            return $response->withStatus(400);
        }
    }
    //* todos los generos (d)
    public function list($request, $response, $args){
        $db = new DB();
        try {
            $generos = $db->makeQuery('SELECT * FROM generos')->fetch_all(MYSQLI_ASSOC);
            //*forma de enviar las exepciones
            if (sizeof($generos) === 0)throw new Exception("No hay generos", 404);
            $response->getBody()->write(json_encode($generos));
            return $response->withStatus(200);
        } catch (Exception $e) {
            $response->getBody()->write($e->getMessage());
            return $response->withStatus(404);
        }
        //todo: catch pdo exception para la conexion de la base de datos
    }
}
?>    
