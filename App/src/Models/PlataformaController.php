<?php
namespace App\src\Models;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\src\Models\DB;
use Exception;

class PlataformasController{
    //* todos los plataformas () --> ver README.md
    public function list(Request $request, Response $response, $args){
        $db = new DB();
        try {
            $plataformas = $db->makeQuery('SELECT * FROM plataformas')->fetchAll();
            if (count($plataformas) === 0) throw new Exception("No hay plataformas", 404);
            $response->getBody()->write(json_encode($plataformas));
            $db->close();
            return $response->withStatus(200);
        } 
        catch (Exception $e) {
            $db->close();
            $response->getBody()->write("Su solicitud arrojó un error: ");
            $response->getBody()->write($e->getMessage());
            return $response->withStatus(404);
        }
    }

    //* crea un plataforma (e) --> ver README.md
    public function create(Request $request, Response $response, $args){
        $db = new DB();
        $body = json_decode($request->getBody(), true);
        if (!isset($body['nombre'])) throw new Exception("Error: Campos vacíos");
        $nombre = $body['nombre'];
        $db->makeQuery("INSERT INTO plataformas (nombre) VALUES (?)", [$nombre]);
        $db->close();
        $response->getBody()->write("Plataforma $nombre creada con éxito");
        return $response->withStatus(200);
    }
    //* actualizar plataforma con id (f) --> ver README.md
    public function update(Request $request, Response $response, $args){
        $db = new DB();
        try {
            if (!isset($args['id'])) throw new Exception("No se recibió el id para hacer el update", 400);
            if (!is_numeric($args['id'])) throw new Exception("El id debe ser numérico", 400);
            if (!$db->existsIn('plataformas', $args['id'])) throw new Exception("No se encontró el id: '" . $args['id'] . "'", 404);
            $body = json_decode($request->getBody(), true);
            if (!isset($body['nombre'])) throw new Exception("No ingresó el nombre del plataforma a actualizar", 400);
            $db->makeQuery("UPDATE plataformas SET nombre = ? WHERE id = ?", [$body['nombre'], $args['id']]);
            $db->close();
            $response->getBody()->write("Plataforma actualizada con éxito");
            return $response->withStatus(200);
        }
        catch (Exception $e) {
            $db->close();
            $response->getBody()->write("Su solicitud arrojó un error: ");
            $response->getBody()->write($e->getMessage());
            return $response->withStatus($e->getCode());
        }
    }
    
    //* delete (g)
    public function delete(Request $request, Response $response, $args){
        $db = new DB();
        try {
            if (!isset($args['id'])) throw new Exception("No se recibió el id", 400);
            if (!is_numeric($args['id'])) throw new Exception("El id debe ser numérico", 400);
            if (!$db->existsIn('plataformas', $args['id'])) throw new Exception("No se encontró el id: '" . $args['id'] . "'", 404);
            $db->makeQuery("DELETE FROM plataformas WHERE id = ?", [$args['id']]);
            $db->close();
            $response->getBody()->write("Plataforma eliminada con éxito");
            return $response;
        }
        catch (Exception $e) {
            $db->close();
            $response->getBody()->write("Su solicitud arrojó un error: ");
            $response->getBody()->write($e->getMessage());
            return $response->withStatus(400);
        }
    }
}