<?php
namespace App\src\Models;
use App\src\Models\DB;
use Exception;
use PDOStatement;

class JuegosController{
    //*post 201 si se creo bien

    //* crea un juego (a)
    public function create($request, $response, $args){
        $db = new DB();
        $body = json_decode($request->getBody(), true);
        if (!isset($body['nombre'], $body['url'], $body['imagen'], $body['tipo_imagen'], $body['descripcion'])) throw new Exception("no se recibieron todos los parametros", 400);
    	$stmt = $db->prepare('INSERT INTO juegos (nombre, imagen, tipo_imagen, descripcion, url, id_genero, id_plataforma) VALUES (:v1,:v2,:v3,:v4,:v5,:v6,:v7)');	
	$stmt->execute(array(
        	':v1' => $body['nombre'],
        	':v2' => $body['imagen'],
		':v3' => $body['tipo_imagen'],
		':v4' => $body['descripcion'],
		':v5' => $body['url'],
		':v6' => $body['id_genero'],
		':v7' => $body['id_plataforma']
		)
	);
	/*
	$db->makeQuery("INSERT INTO juegos VALUES ('" . $body['nombre']. $body['imagen']. $body['tipo_imagen']. $body['descripcion'] . $body['url'] . $body['id_genero'] . $body['id_plataforma'] . "')");
     	*/
	return $response->withStatus(200);
    }

    //* actualizar juego con id (b)
    public function update($request, $response, $args){
        $db = new DB();
        try{
            if (!is_numeric($args['id'])) throw new Exception("el id debe ser numerico", 400);
            if (!isset($args['id'])) throw new Exception("no se recibio el id para hacer el uptdate", 400);
            if (!$db->existsIn('juegos', $args['id'])) throw new Exception("No se encontro el id: '" . $args['id'] . "'", 404);
            $body = json_decode($request->getBody(), true);
            $fieldsToUpdate = isset($body['fields']) ? $body['fields'] : [];
            if(empty($fieldsToUpdate)) throw new Exception("No se ingresaron campos para actualizar", 400);
            
            $query = "SELECT * FROM juegos WHERE id = '" . $body['id'] . "'";
            $juego = $db->makeQuery($query)->fetchAll();

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
    //* todos los juegos (d)
    public function list($request, $response, $args){
        $db = new DB();
        try {
            $juegos = $db->makeQuery('SELECT * FROM juegos')->fetchAll();
            //*forma de enviar las exepciones
            if (sizeof($juegos) === 0)throw new Exception("No hay juegos", 404);
            $response->getBody()->write(json_encode($juegos));
            return $response->withStatus(200);
	} 
	catch (Exception $e) {
            $response->getBody()->write($e->getMessage());
            return $response->withStatus(404);
        }
        //todo: catch pdo exception para la conexion de la base de datos
    }
}
?>    
