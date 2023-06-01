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
        $db = new DB();
        try {
            if (!is_numeric($args['id'])) throw new Exception("el id debe ser numérico", 400);
            if (!isset($args['id'])) throw new Exception("no se recibió el id para hacer el delete", 400);
            if (!$db->existsIn('plataformas', $args['id'])) throw new Exception("No se encontró el id: '" . $args['id'] . "'", 404);
            
            $resul =$db->makeQuery("DELETE FROM plataformas WHERE id = ?", [$args['id']])->fetchAll();

            $db->close();
            return $response->withStatus(200);
        } catch (Exception $e) {
            $response->getBody()->write($e->getMessage());
            $db->close();
            return $response->withStatus($e->getCode());
        }
    }
    public function juegosAll($request, $response, $args){
        $db = new DB();
        $respuesta = $db->makeQuery('SELECT * FROM juegos')->fetchAll();
        $response->getBody()->write(json_encode($respuesta));
        return $response->withStatus(200);
    }
    public function juegos(Request $request, Response $response, $args){
        $db = new DB();
        try{
            $genero = $request->getQueryParams()['genero'] ?? null;
            $nombre = $request->getQueryParams()['nombre'] ?? null;
            $plataforma = $request->getQueryParams()['plataforma'] ?? null;
            if ($genero === null && $plataforma === null && $nombre === null) throw new Exception("se debe dar un parametro (genero o plataforma o nombre)", 400);
            $asc = $request->getQueryParams()['asc']?? false;
            $datos = [];
            $query = "SELECT * FROM juegos WHERE 1=1 ";
            if($genero != null){
                $query.="AND id_genero = ?";
                array_push($datos,$genero);
            }
            if($nombre != null){
                $query .= "AND nombre like ?";
                $nombre = "%".$nombre."%";
                array_push($datos, $nombre);
            }
            if($plataforma!= null){
                $query.="AND plataforma =?";
                array_push($datos, $plataforma);
            }
            if($asc)$query.=" ORDER BY nombre ASC ";
            $respuesta = $db->makeQuery($query, $datos)->fetchAll();
            if(count($respuesta) === 0)throw new Exception("No se encontro el juego", 404);
            $response->getBody()->write(json_encode($respuesta));
            $db->close();
            return $response->withStatus(200);
        }
        catch(Exception $e){
            $response->getBody()->write($e->getMessage());
            $db->close();
            return $response->withStatus(404);
        }
    }
    public function createJuego($request, $response, $args)
    {
        $db = new DB();
        $body = json_decode($request->getBody(), true);
        try {
        //!falta ver si se envian los id_genero y id_plataforma
        if (!isset($body['nombre'], $body['url'], $body['imagen'], $body['tipo_imagen'], $body['descripcion'], $body['id_genero'], $body['id_plataforma'])) throw new Exception("no se recibieron todos los parametros", 400);
        $params = array(
            ':v1' => $body['nombre'],
            ':v2' => $body['imagen'],
            ':v3' => $body['tipo_imagen'],
            ':v4' => $body['descripcion'],
            ':v5' => $body['url'],
            ':v6' => $body['id_genero'],
            ':v7' => $body['id_plataforma']
        );
        
        //!falta validar los datos (tipo de imagen,cant char, obligatorios)
        $query = 'INSERT INTO juegos (nombre, imagen, tipo_imagen, descripcion, url, id_genero, id_plataforma) VALUES (:v1,:v2,:v3,:v4,:v5,:v6,:v7)';
        $db->makeQuery($query,$params);
        return $response->withStatus(200);
        } catch (Exception $e) {
            $response->getBody()->write($e->getMessage());
            return $response->withStatus($e->getCode());
        }
    }
    public function updateJuegos($request, $response, $args)
    {
        $db = new DB();
        try {
            if (!is_numeric($args['id'])) throw new Exception("El id debe ser numerico", 400);
            if (!isset($args['id'])) throw new Exception("No se recibio el id para hacer el update", 400);
            if (!$db->existsIn('juegos', $args['id'])) throw new Exception("No se encontro el id: '" . $args['id'] . "'", 404);
            $body = json_decode($request->getBody(), true);
            $query = "UPDATE juegos SET "; // vamos a ir generando la query de a partes
            $bindings = [];
            foreach ($body as $field => $value) { //este for each mapea bindings con campos ingresados 
                $query .= "$field = :$field, ";
                $bindings[":$field"] = $value;
            }
            $query = rtrim($query, ', '); // elimina la última coma de la query agregada en la última iteración del foreach
            $query .= " WHERE id = :id";
            $bindings[':id'] = $args['id'];
            $db->makeQuery($query, $bindings)->fetchAll(); //makeQuery prepara la $query y luego la ejecuta con los $bindings
            $response->getBody()->write("Se actualizo bien");
            return $response->withStatus(200);
        } 
        catch (Exception $e) {
            $response->getBody()->write($e->getMessage());
            return $response->withStatus(404);
        }
    }

}
