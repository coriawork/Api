<?php
namespace App\src\Models;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\src\Models\DB;
use Exception;

class PlataformaController{
    public function create($request, $response, $args){
        $db = new DB();
        try{          
            $body = json_decode($request->getBody(), true);
            if (!isset($body['nombre'])) throw new Exception("no se recibió el parámetro", 400);
            $nombre = $body['nombre'];
            $db->makeQuery("INSERT INTO plataformas (nombre) VALUES (?)", [$nombre]);
            $db->close();
            return $response->withStatus(201);
        }
        catch(Exception $e) {
            $response->getBody()->write($e->getMessage());
            $db->close();
            return $response->withStatus($e->getCode());
        }
    }
    
    public function list($request, $response, $args){
        $db = new DB();
        try {
            $plataformas = $db->makeQuery('SELECT * FROM plataformas')->fetchAll();
            if (count($plataformas) === 0) throw new Exception("No hay plataformas", 404);
            $response->getBody()->write(json_encode($plataformas));
            $db->close();
            return $response->withStatus(200);
        } catch (Exception $e) {
            $response->getBody()->write($e->getMessage());
            $db->close();
            return $response->withStatus(404);
        }
    }
    
    public function update($request, $response, $args){
        $db = new DB();
        try {
            if (!is_numeric($args['id'])) throw new Exception("el id debe ser numérico", 400);
            if (!isset($args['id'])) throw new Exception("no se recibió el id para hacer el update", 400);
            if (!$db->existsIn('plataformas', $args['id'])) throw new Exception("No se encontró el id: '" . $args['id'] . "'", 404);
            
            $body = json_decode($request->getBody(), true);
            if (!isset($body['nombre'])) throw new Exception("no se recibió el parámetro para el update", 400);
            
            $db->makeQuery("UPDATE plataformas SET nombre = ? WHERE id = ?", [$body['nombre'], $args['id']]);
            $db->close();
            return $response->withStatus(200);
        } catch (Exception $e) {
            $response->getBody()->write($e->getMessage());
            $db->close();
            return $response->withStatus($e->getCode());
        }
    }
    
    public function delete($request, $response, $args){
        /* Esta función recibe un DEL request con un ID de plataforma como argumento y elimina la misma */
        $db = new DB();
        try {
            if (!is_numeric($args['id'])) throw new Exception("el id debe ser numérico", 400);
            if (!isset($args['id'])) throw new Exception("no se recibió el id para hacer el delete", 400);
            if (!$db->existsIn('plataformas', $args['id'])) throw new Exception("No se encontró el id: '" . $args['id'] . "'", 404);
            
            $resul =$db->makeQuery("DELETE FROM plataformas WHERE id = ?", [$args['id']])->fetchAll();

            $db->close();
            return $response->withStatus(200);
        } 
        catch (Exception $e) {
            $response->getBody()->write($e->getMessage());
            $db->close();
            return $response->withStatus($e->getCode());
        }
    }
}