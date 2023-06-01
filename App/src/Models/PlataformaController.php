<?php
namespace App\src\Models;
use App\src\Models\DB;
use Exception;
Class PlataformaController{
    public function create($request, $response, $args){
        $db = new DB();
        try{          
            $body = json_decode($request->getBody(), true);
            if (!isset($body['nombre'])) throw new Exception("no se recibio el parametro", 400);
            $nombre=$body['nombre'];
            $db->makeQuery("INSERT INTO plataformas (nombre) VALUES ('" . $nombre . "')");
            return $response->withStatus(200);
        }
        catch(Exception $e) {
            $response->getBody()->write($e->getMessage());
            return $response->withStatus($e->getCode());
        }
    }
    public function list($request, $response, $args){
        $db = new DB();
        try {
            $generos = $db->makeQuery('SELECT * FROM plataformas')->fetchAll(MYSQLI_ASSOC);
            //*Envio de Exepciones
            if (sizeof($generos) === 0) throw new Exception("No hay generos", 404);

            $response->getBody()->write(json_encode($generos));
            return $response->withStatus(200);
        } catch (Exception $e) {
            $response->getBody()->write($e->getMessage());
            return $response->withStatus($e->getCode());
        }
        //todo: catch pdo exception para la conexion de la base de datos
    }
    public function update($request, $response, $args){
        $db = new DB();
        try {
            if(!is_numeric($args['id'])) throw new Exception("el id debe ser numerico", 400);
            if(!isset($args['id'])) throw new Exception("no se recibio el id para hacer el uptdate", 400);
            if(!$db->existsIn('Plataformas',$args['id'])) throw new Exception("No se encontro el id: '" . $args['id'] . "'", 404);

            $body = json_decode($request->getBody(), true);

            if(!isset($body['nombre']))throw new Exception("no se recibio el parametro para update", 400);

            $db->makeQuery("UPDATE plataformas SET nombre='" . $body['nombre'] . "' WHERE id='" . $args['id'] . "'");
            return $response->withStatus(200);
        } catch (Exception $e) {
            $response->getBody()->write($e->getMessage());
            return $response->withStatus($e->getCode());
        }
    }
    public function delete($request, $response, $args){
        $db = new DB();
        try {
            if(!is_numeric($args['id'])) throw new Exception("el id debe ser numerico", 400);
            if(!isset($args['id'])) throw new Exception("no se recibio el id para hacer el uptdate", 400);
            if(!$db->existsIn('Plataformas',$args['id'])) throw new Exception("No se encontro el id: '". $args['id']. "'", 404);

            $db->makeQuery("DELETE FROM plataformas WHERE id='". $args['id']. "'");
            return $response->withStatus(200);
        }
        catch(Exception $e){
            $response->getBody()->write($e->getMessage());
            return $response->withStatus($e->getCode());
        }
    }
}