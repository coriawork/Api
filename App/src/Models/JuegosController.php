<?php
namespace App\src\Models;
use App\src\Models\DB;
use Exception;

class JuegosController{
    //*post 201 si se creo bien

    //* crea un juego
    public function create($request, $response, $args){
        $db = new DB();
        $body = json_decode($request->getBody(), true);
        if (!isset($body['nombre'], $body['url'], $body['imagen'], $body['tipo_imagen'], $body['descripcion'])) throw new Exception("no se recibieron todos los parametros", 400);
    	$query = $db->prepare('INSERT INTO juegos (nombre, imagen, tipo_imagen, descripcion, url, id_genero, id_plataforma) VALUES (:v1,:v2,:v3,:v4,:v5,:v6,:v7)');	
	    $params = array(
        	':v1' => $body['nombre'],
        	':v2' => $body['imagen'],
            ':v3' => $body['tipo_imagen'],
            ':v4' => $body['descripcion'],
            ':v5' => $body['url'],
            ':v6' => $body['id_genero'],
            ':v7' => $body['id_plataforma']
		    );
        try {
            $db->makeQuery($query, $params);
            return $response->withStatus(200);
        }
        catch(Exception $e) {
            $response->getBody()->write($e->getMessage());
            return $response->withStatus($e->getCode());
        }
    }

    //* actualizar juego con id 
    public function update($request, $response, $args) {
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

            $db->makeQuery($query, $bindings); //makeQuery prepara la $query y luego la ejecuta con los $bindings
        }
        catch (Exception $e) {
            $response->getBody()->write($e->getMessage());
            return $response->withStatus($e->getCode());
        }
    }
    //*delete
    public function delete($request, $response, $args){
        $db = new DB();
        try{
            $body = json_decode($request->getBody(), true);
            $result = $db->makeQuery("SELECT * from juegos where id = '" . $body['id'] . "'");
            if($result->rowCount() === 0) throw new Exception("No existe el id", 400);
            if (!isset($body['id'])) throw new Exception("No se recibio el id", 400);
            $result = $db->makeQuery("DELETE FROM juegos where id = '".$body['id']."'");
            return $response;
        }
        catch(Exception $e){
            $response->getBody()->write($e->getMessage());
            return $response->withStatus(400);
        }
    }
    // todos los juegos 
    public function list($request, $response, $args){
        $db = new DB();
        try {
            $juegos = $db->makeQuery('SELECT * FROM juegos')->fetchAll();
            //forma de enviar las exepciones
            if (sizeof($juegos) === 0)throw new Exception("No hay juegos", 404);
            $response->getBody()->write(json_encode($juegos));
            return $response->withStatus(200);
	    } 
        catch (Exception $e) {
            $response->getBody()->write($e->getMessage());
            return $response->withStatus(404);
        }
    }    
    public function search($request, $response, $args)
    {
        $db = new DB();
        try {
            $body = json_decode($request->getBody(), true);
            $query = "SELECT * FROM juegos WHERE ";
            $bindings = [];
            $length = count($body);
            for ($i = 0; $i < $length - 1; $i++) {
                $query .= "{$body[$i]} = :{$body[$i]}, ";
                $bindings[":{$body[$i]}"] = $body[$i];
            }
            $query = rtrim($query, ', '); // remuevo la última coma de la query
            $bindings[":{$body['orden']}"] = $body['orden'];
            $query .= " ORDER BY {$lastValue}";
    
            $db->makeQuery($query, $bindings);
    
            return $response->withStatus(200);
        } 
        catch (Exception $e) {
            $response->getBody()->write($e->getMessage());
            return $response->withStatus(404);
        }
    }
}
?>    
