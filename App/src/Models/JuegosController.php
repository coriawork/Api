<?php
namespace App\src\Models;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\src\Models\DB;
use Exception;

class JuegosController{

    public function juegosAll($request, $response, $args){
        /*
        Esta funcion recibe un get request y devuelve todos los juegos
        */
        $db = new DB();
        $respuesta = $db->makeQuery('SELECT * FROM juegos')->fetchAll();
        $response->getBody()->write(json_encode($respuesta));
        return $response->withStatus(200);
    }  
    public function juegos (Request $request, Response $response, $args){
        /*
        Esta función recibe un GET request con parámetros para buscar juego.
        Es obligatorio que exista al menos uno de estos tres parámetros:
            -genero(str).
            -plataforma(str).
            -nombre(str).
        Es opcional el siguiente parámetro:
            -orden(boolean): orden predefinido = ASC.
        */

        $db = new DB();
        try {
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
        catch (Exception $e) {
            $response->getBody()->write($e->getMessage());
            $db->close();
            return $response->withStatus(404);
        }
    }
    public function createJuego ($request, $response, $args) {
        /*
        Esta función recibe un POST request para agregar un juego nuevo.
        Respeta y valida las condiciones previamente establecidas en la Entrega nº1 en cuanto
        a la creación de un nuevo juego vía formulario de html.
        Por tanto, es obligatorio que la request contenga los siguientes campos:
            nombre(str),
            imagen(blob en base64),
            tipo_imagen(str): debe ingresarse un tipo válido: png o jpg,
            id_plataforma(int): debe ser un id válido de una plataforma existente
        Los siguientes campos son opcionales:
            descripcion(str): no más de 255 caracteres.
            url(str): no más de 88 caracteres.
            id_genero(): debe ser un id válido de un genero existente.
        Si la creación del juego es exitosa se imprime un mensaje junto con un Status200 en la response.
        */
        function validarCreacionJuego($body, $response) {
            /*
            Esta función recibe el cuerpo de la request. Imprime mensajes indicando si hubo o no hubo errores en la validación.
            Es una función local que solo se debe invocar cuando se crea un juego, por eso el alcance otorgado.
            */
            $huboErr =false;

            if(empty($body['name'])){
                $err1 = "Campo nombre requerido ";
                $huboErr =true;
            }
            if (strlen($body["descripcion"]) > 255) {
                $err2 = "La descripcion debe de ser de menos de 255 caracteres";
                $huboErr =true;
            }
            if (strlen($body["url"]) > 88) {
                $err3 = "La url debe de ser de menos de 88 caracteres";
                $huboErr =true;
            }
            if (empty($body['plataforma'])) {
                $err4 = "Campo plataforma requerido";
                $huboErr = true;
            }
            if (empty($body['genero'])) {
                $err6 = "Campo plataforma requerido";
                $huboErr = true;
            }
            if(empty($body['imagen'])){
                $err4 = "La imagen es un campo requerido";
                $huboErr = true;
            }
            if (!in_array($body['tipo_imagen'], ['jpg', 'png'])) {
                $err5 = "el archivo no es un formato de imagen válido";
                $huboErr = true;
            }
            /* Si hubo errores: arrojar excepcion con los mismos con write en el body y status 400*/
            if ($huboErr) {
                $errors = ($err1.','.$err2.','.$err3 .','.$err4.','.$err5 . ',' . $err6. ',');
                //$response->getBody()->write($errors);
                //$response->withStatus(400);
                throw new Exception($errors, 400);
            }
            /* Caso contrario, mensaje en body de que se validó bien*/
            else {
                $response->getBody()->write("Validación exitosa, los campos cumplen los requisitos para la creación...");
            }
        }
        $db = new DB();
        $body = json_decode($request->getBody(), true);
        try {
            // Preguntamos si estan los parámetros obligatorios antes de validar            
            if (!isset($body['nombre'], $body['url'], $body['imagen'], $body['tipo_imagen'], $body['descripcion'], $body['id_genero'], $body['id_plataforma'])) throw new Exception("Faltan parámetros", 400);
            validarCreacionJuego($body, $response);
            $params = array(
                ':v1' => $body['nombre'],
                ':v2' => $body['imagen'],
                ':v3' => $body['tipo_imagen'],
                ':v4' => $body['descripcion'],
                ':v5' => $body['url'],
                ':v6' => $body['id_genero'],
                ':v7' => $body['id_plataforma']
                );
            $query = 'INSERT INTO juegos (nombre, imagen, tipo_imagen, descripcion, url, id_genero, id_plataforma) VALUES (:v1,:v2,:v3,:v4,:v5,:v6,:v7)';
            $db->makeQuery($query,$params);
            $response->getBody()->write("Se actualizo bien");
            return $response->withStatus(200);
        }
        catch (Exception $e) {
            $response->getBody()->write($e->getMessage());
            return $response->withStatus($e->getCode());
        }
    }

    public function updateJuegos($request, $response, $args) {
        /*
        Esta función recibe un PUT request con un id de juego y con parámetros para actualizarlo.
        Son obligatorios:
            args: id(int)
            body: es obligatorio al menos uno de los siguientes campos:
                nombre(str),
                imagen(blob en base64),
                tipo_imagen(str): solo tipo de imagenes validas como .jpg o .png,
                descripcion(str): no más de 255 char,
                url(str): no más de 80 char,
                id_genero: id de genero existente,
                id_plataforma: id de plataforma existente
        */
        function validarArgsUpdate($args, $db) {
            /*Se validan los argumentos chequeando que:
            -Se recibió el id como argumento.
            -El id es numerico.
            */
            if (!isset($args['id'])) throw new Exception("No se recibio el id para hacer el update", 400);
            if (!is_numeric($args['id'])) throw new Exception("El id debe ser numerico", 400);
        }
        function validarUpdateJuego ($db, $body, $response, $args) {
            $huboErr = false;
            if (!$db->existsIn('juegos', $args['id'])) throw new Exception("No se encontro el id: '" . $args['id'] . "'", 404);
            if (strlen($body["descripcion"]) > 255) {
                $err2 = "La descripcion debe de ser de menos de 255 caracteres";
                $huboErr =true;
            }
            if (strlen($body["url"]) > 88) {
                $err3 = "La url debe de ser de menos de 88 caracteres";
                $huboErr =true;
            if(empty($body['imagen']))
                $err4 = "No ingresó ninguna imagen en base64. Campo vacío.";
                $huboErr = true;
            }
            if (!in_array($body['tipo_imagen'], ['jpg', 'png'])) {
                $err5 = "El archivo no es un formato de imagen válido. Solo se permite 'jpg' y 'png'";
                $huboErr = true;
            }
            /* Si hubo errores: arrojar excepcion con los mismos con write en el body y status 400*/
            if ($huboErr) {
                $errors = ($err2.','.$err3 .','.$err4.','.$err5);
                //$response->getBody()->write($errors);
                //$response->withStatus(400);
                throw new Exception($errors, 400);
            }
            /* Caso contrario, mensaje en body de que se validó bien*/
            else {
                $response->getBody()->write("Validación exitosa, los campos cumplen los requisitos para la actualización...");
            }
        }
        $db = new DB();
        validarArgsUpdate($args, $db);
        $body = json_decode($request->getBody(), true);
        validarUpdateJuego($db, $body, $response, $args);
        try {
            $query = "UPDATE juegos SET ";
            $bindings = [];
            foreach ($body as $field => $value) { //este for each mapea bindings con campos ingresados 
                $query .= "$field = :$field, ";
                $bindings[":$field"] = $value;
            }
            $query = rtrim($query, ', '); // elimina la última coma de la query agregada en la última iteración del foreach
            $query .= " WHERE id = :id";
            $bindings[':id'] = $args['id'];
            $db->makeQuery($query, $bindings)->fetchAll();
            $response->getBody()->write("Se actualizo bien");
            return $response->withStatus(200);
        } 
        catch (Exception $e) {
            $response->getBody()->write($e->getMessage());
            return $response->withStatus(404);
        }
    }
    public function delete($request, $response, $args){
        function validarDelete($args, $db, $body) {
            $result = $db->makeQuery("SELECT * from juegos where id = '" . $body['id'] . "'");
            if($result->rowCount() === 0) throw new Exception("No existe el id", 400);
            if (!isset($body['id'])) throw new Exception("No se recibio el id", 400);
        }

        $db = new DB();
        $body = json_decode($request->getBody(), true);
        validarDelete($args, $db, $body);
        try {
            $db->makeQuery("DELETE FROM juegos where id = '".$body['id']."'");
            return $response;
        }
        catch (Exception $e) {
            $response->getBody()->write($e->getMessage());
            return $response->withStatus(400);
        }
    }
}
?>    
