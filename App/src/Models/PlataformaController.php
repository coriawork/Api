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
            
            $db->makeQuery("DELETE FROM plataformas WHERE id = ?", [$args['id']]);
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
        $parms = $request->getQueryParams();
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
                if(!$db->existsIn('generos',$genero))throw new Exception("el genero no existe",404); //no se si estas exepciones son necesarias o las debe enviar la base de datos
                $query.="AND id_genero = ?";
                array_push($datos,$genero);
            }
            if($nombre != null){
                $query.="AND nombre like ?";
                array_push($datos, $nombre);
            }
            if($plataforma!= null){
                if (!$db->existsIn('plataformas', $plataforma)) throw new Exception("la plataforma no existe", 404);

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
            return $response->withStatus($e->getCode());
        }
    }
}
