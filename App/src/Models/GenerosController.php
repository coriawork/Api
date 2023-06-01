<?php
namespace App\src\Models;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\src\Models\DB;
use Exception;

class GenerosController{
    //* todos los generos (d) --> ver README.md
    public function list(Request $request, Response $response, $args){
        $db = new DB();
        try {
            $generos = $db->makeQuery('SELECT * FROM generos')->fetchAll();
            if (count($generos) === 0) throw new Exception("No hay generos", 404);
            $response->getBody()->write(json_encode($generos));
            $db->close();
            return $response->withStatus(200);
        } catch (Exception $e) {
            $response->getBody()->write($e->getMessage());
            $db->close();
            return $response->withStatus(404);
        }
        //todo: catch PDOException para la conexión de la base de datos
    }

    //* crea un genero (a) --> ver README.md
    public function create(Request $request, Response $response, $args){
        $db = new DB();
        $nombre = json_decode($request->getBody(), true)['nombre'];
        $db->makeQuery("INSERT INTO generos (nombre) VALUES (?)", [$nombre]);
        $db->close();
        $response->getBody()->write("Juego $nombre creado con éxito");
        return $response->withStatus(200);
    }

    //* actualizar genero con id (b) --> ver README.md
    public function update(Request $request, Response $response, $args){
        $db = new DB();
        try{
            print_r($args);
            if (!isset($args['id'])) throw new Exception("No se recibió el id para hacer el update", 400);
            if (!is_numeric($args['id'])) throw new Exception("El id debe ser numérico", 400);
            if (!$db->existsIn('generos', $args['id'])) throw new Exception("No se encontró el id: '" . $args['id'] . "'", 404);

            $body = json_decode($request->getBody(), true);
            if (!isset($body['nombre'])) throw new Exception("no se recibió el parámetro para el update", 400);
            $db->makeQuery("UPDATE generos SET nombre = ? WHERE id = ?", [$body['nombre'], $args['id']]);
            $db->close();
            return $response->withStatus(200);
        }
        catch(Exception $e){
            $response->getBody()->write($e->getMessage());
            $db->close();
            return $response->withStatus($e->getCode());
        }
    }
    
    //* delete (c)
    public function delete(Request $request, Response $response, $args){
        $db = new DB();
        try{
            $body = json_decode($request->getBody(), true);
            if (!isset($body['id'])) throw new Exception("No se recibió el id", 400);
            if (!$db->existsIn('generos', $body['id'])) throw new Exception("No existe el id", 404);
            $db->makeQuery("DELETE FROM generos WHERE id = ?", [$body['id']]);
            $db->close();
            return $response;
        }
        catch(Exception $e){
            $response->getBody()->write($e->getMessage());
            $db->close();
            return $response->withStatus(400);
        }
    }
}