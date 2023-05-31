<?php
namespace App\src\Models;
use App\src\Models\DB;
use Exception;

class JuegosController{
    //*post 201 si se creo bien

    //* crea un juego (a)
    public function create($request, $response, $args){
        $db = new DB();
        $body = json_decode($request->getBody(), true);
        $db->makeQuery("INSERT INTO juegos (nombre) VALUES ('" . $body['url'], $body['nombre'],
        $body['imagen'], $body['tipo_imagen'], $body['descripcion'] . "')");
        return $response->withStatus(200);
    }

    //* actualizar juego con id (b)
    public function update($request, $response, $args){
        $db = new DB();
        try{
            if (!is_numeric($args['id'])) throw new Exception("el id debe ser numerico", 400);
            if (!isset($args['id'])) throw new Exception("no se recibio el id para hacer el uptdate", 400);
            if (!$db->ExistIn('juegos', $args['id'])) throw new Exception("No se encontro el id: '" . $args['id'] . "'", 404);
            $body = json_decode($request->getBody(), true);
            $fieldsToUpdate = isset($body['fields']) ? $body['fields'] : [];
            if(empty($fieldsToUpdate)) throw new Exception("No se ingresaron campos para actualizar", 400);
            
            $query = "SELECT * FROM juegos WHERE id = '" . $body['id'] . "'";
            $juego = $db->makeQuery($query)->fetch_assoc()
            // Step 4: Compare and update fields
            $updateFields = [];
            foreach ($fieldsToUpdate as $field => $value) {
                if (array_key_exists($field, $existingRecord)) {
                    $updateFields[$field] = $value;
                }
            }

            // Return a response indicating the successful update

            // Close the mysqli connection
            $db->close();
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
            if($result->num_rows === 0) throw new Exception("No existe el id", 400);
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
            $juegos = $db->makeQuery('SELECT * FROM juegos')->fetch_all(MYSQLI_ASSOC);
            //*forma de enviar las exepciones
            if (sizeof($juegos) === 0)throw new Exception("No hay juegos", 404);
            $response->getBody()->write(json_encode($juegos));
            return $response->withStatus(200);
        } catch (Exception $e) {
            $response->getBody()->write($e->getMessage());
            return $response->withStatus(404);
        }
        //todo: catch pdo exception para la conexion de la base de datos
    }
}
?>    
