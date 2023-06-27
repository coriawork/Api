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
            $response = $response->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Methods', 'GET, OPTIONS')
            ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization');
            return $response->withHeader('content-type', 'application/json')->withStatus(200);
        } 
        catch (Exception $e) {
            $response->getBody()->write("Su solicitud arrojó un error: ");
            $response->getBody()->write($e->getMessage());
            $db->close();
            return $response->withStatus(404);
        }
    }

    //* crea un genero (a) --> ver README.md
    public function create(Request $request, Response $response, $args){
        $db = new DB();
        $body = json_decode($request->getBody(), true);
        if (!isset($body['nombre'])) throw new Exception("Error: Campos vacíos");
        $nombre = $body['nombre'];
        $db->makeQuery("INSERT INTO generos (nombre) VALUES (?)", [$nombre]);
        $db->close();
        $response->getBody()->write("Genero $nombre creado con éxito");
        return $response->withStatus(200);
    }

    //* actualizar genero con id (b) --> ver README.md
    public function update(Request $request, Response $response, $args){
        $db = new DB();
        try {
            if (!isset($args['id'])) throw new Exception("No se recibió el id para hacer el update", 400);
            if (!is_numeric($args['id'])) throw new Exception("El id debe ser numérico", 400);
            if (!$db->existsIn('generos', $args['id'])) throw new Exception("No se encontró el id: '" . $args['id'] . "'", 404);
            $body = json_decode($request->getBody(), true);
            if (!isset($body['nombre'])) throw new Exception("No ingresó el nombre del genero a actualizar", 400);
            $db->makeQuery("UPDATE generos SET nombre = ? WHERE id = ?", [$body['nombre'], $args['id']]);
            $db->close();

            /* $response = $response->withHeader('Access-Control-Allow-Origin', '*')
                ->withHeader('Access-Control-Allow-Methods', 'PUT, OPTIONS')
                ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization');
 */

            $response->getBody()->write("Genero actualizado con éxito");
            return $response->withStatus(200);
        }
        catch (Exception $e) {
            $db->close();
            $response->getBody()->write("Su solicitud arrojó un error: ");
            $response->getBody()->write($e->getMessage());
            return $response->withStatus($e->getCode());
        }
    }
    
    //* delete (c)
    public function delete(Request $request, Response $response, $args){
        $db = new DB();
        try {
            if (!isset($args['id'])) throw new Exception("No se recibió el id", 400);
            if (!is_numeric($args['id'])) throw new Exception("El id debe ser numérico", 400);
            if (!$db->existsIn('generos', $args['id'])) throw new Exception("No se encontró el id: '" . $args['id'] . "'", 404);
            $db->makeQuery("DELETE FROM generos WHERE id = ?", [$args['id']]);
            $db->close();
            $response->getBody()->write("Genero eliminado con éxito");
            /* $response = $response->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Methods', 'GET,PUT,POST,DELETE,PATCH,OPTIONS')
            ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization'); */
   
            return $response->withStatus(200);
        }
        catch (Exception $e) {
            $db->close();
            $response = $response->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Methods', 'GET,PUT,POST,DELETE,PATCH,OPTIONS')
            ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization');

            $response->getBody()->write("Su solicitud arrojó un error: " . $e->getMessage());
            return $response->withStatus(404);
        }
    }
}