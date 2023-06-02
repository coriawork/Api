<?php
namespace App\src\Models;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\src\Models\DB;
use Exception;
use Throwable;

class JuegosController{
    //*obtener Todos los juegos (h)
    public function juegosAll(Request $request, Response $response, $args){
        /*
        Esta funcion recibe un get request y devuelve todos los juegos
        */
        $db = new DB();
        $respuesta = $db->makeQuery('SELECT * FROM juegos')->fetchAll();
        $response->getBody()->write(json_encode($respuesta));
        return $response->withStatus(200);
    }  
    //*buscar Juegos (m)
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
            $id_plataforma = $request->getQueryParams()['id_plataforma'] ?? null;
            if ($genero === null && $id_plataforma === null && $nombre === null) throw new Exception("se debe dar un parametro (genero o id_plataforma o nombre)", 400);
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
            if($id_plataforma!= null){
                $query.="AND id_plataforma =?";
                array_push($datos, $id_plataforma);
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
    //*Crear Juegos (i)
    public function createJuego (Request $request, Response $response, $args) {
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
        function validarCreacionJuego($body, $res, $db) {
            /*
            Esta función recibe el cuerpo de la request. Imprime mensajes indicando si hubo o no hubo errores en la validación.
            Es una función local que solo se debe invocar cuando se crea un juego, por eso el alcance otorgado.
            */
            $huboErr =false;
            $errors = [];
            if(empty($body['nombre'])){
                $err1 = "Campo nombre requerido ";
                array_push($errors, $err1);
                $huboErr =true;
            }
            if (isset($body["descripcion"]) && strlen($body["descripcion"]) > 255) {
                $err2 = "La descripcion debe de ser de menos de 255 caracteres";
                array_push($errors, $err2);
                $huboErr =true;
            }
            if (isset($body["url"]) && strlen($body["url"]) > 88) {
                $err3 = "La url debe de ser de menos de 88 caracteres";
                array_push($errors, $err3);
                $huboErr =true;
            }
            if (empty($body['id_plataforma'])) {
                $err4 = "Campo plataforma requerido";
                array_push($errors, $err4);
                $huboErr = true;
            }
            if (isset($body["id_genero"]) && (!$db->existsIn('generos', $body['id']))) {
                $err5 = "El id_genero suministrado no es un id valido";
                array_push($errors, $err5);
                $huboErr = true;
            }
            if (empty($body['imagen'])) {
                $err6 = "La imagen es un campo requerido";
                array_push($errors, $err6);
                $huboErr = true;
            }
            if (!in_array($body['tipo_imagen'], ['jpg', 'png'])) {
                $err7 = "El tipo del archivo no es un formato de imagen válido";
                array_push($errors, $err7);
                $huboErr = true;
            }
            if (!$db->existsIn('plataformas', $body['id_plataforma'])) {
                $err8 = "El id_plataforma suministrado no es un id valido";
                array_push($errors, $err8);
                $huboErr = true;
            }
                /* Si hubo errores: arrojar excepcion con los mismos con write en el body y status 400*/
            if ($huboErr) {
                //$res->getBody()->write($errors);
                //$res->withStatus(400);
                throw new Exception(implode(". ", $errors),400);
            }
            /* Caso contrario, mensaje en body de que se validó bien*/
            else {
                $res->getBody()->write("Campos validados...");
            }
        }
        $db = new DB();
        $body = json_decode($request->getBody(), true);
        try {
            // Preguntamos si estan los parámetros obligatorios antes de validar            
            if (!isset($body['nombre'], $body['imagen'], $body['tipo_imagen'], $body['id_plataforma'])) throw new Exception("Faltan parámetros obligatorios. Revisar los mismos en la documentacion de la API https://github.com/coriawork/tp2/blob/Merge/README.md", 400);
                validarCreacionJuego($body, $response, $db);
                $params = array(
                    ':v1' => $body['nombre'],
                    ':v2' => $body['imagen'],
                    ':v3' => $body['tipo_imagen'],
                    ':v4' => $body['id_plataforma'],
                );
                $query_fields = 'INSERT INTO juegos (nombre, imagen, tipo_imagen, id_plataforma,';
                $query_values = 'VALUES (:v1,:v2,:v3,:v4,';
                if (isset($body['descripcion'])) {
                    $params[':v5'] = $body['descripcion'];
                    $query_fields .= 'descripcion, ';
                    $query_values .= ':v5, ';
                }
                if (isset($body['url'])) {
                    $params[':v6'] = $body['url'];
                    $query_fields .= 'url, ';
                    $query_values .= ':v6, ';
                }
                if (isset($body['id_genero'])) {
                    $params[':v7'] = $body['id_genero'];
                    $query_fields .= 'id_genero, ';
                    $query_values .= ':v7, ';
                }
                $query_fields .= ')';
                $query_values .= ')';
                $query_fields = preg_replace('/,+(?=,|\s*\))/','',$query_fields);                
                $query_fields = preg_replace('/,+/',',',$query_fields);
                $query_values = preg_replace('/,+(?=,|\s*\))/','',$query_values);                
                $query_values = preg_replace('/,+/',',',$query_values);
                $query = $query_fields . ' ' . $query_values;
                echo($query);
                $db->makeQuery($query,$params);
                $response->getBody()->write("Se actualizo bien");
                return $response->withStatus(200);
            }
        catch (Exception $e) {
            $response->getBody()->write($e->getMessage());
            return $response->withStatus(400);
        }
    }
    //*Actualizar Juego (j)
    public function updateJuegos(Request $request, Response $response, $args)
    {
        /*
        Esta función recibe un PUT request con un id de juego y con parámetros para actualizarlo.
        */
        $db = new DB();       
        try {
            /*Se validan los argumentos chequeando que:
            -Se recibió el id como argumento.
            -El id es numerico.
            */
            $body = json_decode($request->getBody(), true);
            if (!isset($args['id'])) throw new Exception("No se recibio el id para hacer el update", 400);
            if (!is_numeric($args['id'])) throw new Exception("El id debe ser numerico", 400);
            /**Son obligatorios:
            args: id(int)
            body:
                son obligatorios los campos: nombre,imagen,plataforma 
                resto de campos:
                nombre(str),
                imagen(blob en base64),
                tipo_imagen(str): solo tipo de imagenes validas como .jpg o .png,
                descripcion(str): no más de 255 char,
                url(str): no más de 80 char,
                id_genero: id de genero existente,
                id_plataforma: id de plataforma existente
             */
            if (!$db->existsIn('juegos', $args['id'])) throw new Exception("No se encontro el id: '" . $args['id'] . "'", 400);
            if(!isset($body["descripcion"]) || empty($body['descripcion'])) throw new Exception("la descripcion es obligatoria", 400);
            if (!isset($body["nombre"]) || empty($body['nombre'])) throw new Exception("el nombre es obligatorio", 400);
            if (strlen($body["descripcion"]) > 255) throw new Exception("La descripcion debe de ser de menos de 255 caracteres", 400);
            if(isset($body["url"]) && strlen($body["url"]) > 88) throw new Exception("La url debe de ser de menos de 88 caracteres", 400);
            if(!isset($body["imagen"]) || empty($body['imagen'])) throw new Exception("la imagen es obligatoria", 400);
            if (!isset($body["tipo_imagen"]) || empty($body['tipo_imagen'])) throw new Exception("la tipo de imagen es obligatoria", 400);
            if(!in_array($body['tipo_imagen'], ['jpg', 'png'])) throw new Exception("El archivo no es un formato de imagen válido. Solo se permite 'jpg' y 'png'", 400);;

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
            $response->getBody()->write("Validación exitosa, los campos cumplen los requisitos para la actualización...");
            return $response->withStatus(200);
        } catch (Exception $e) {
            $response->getBody()->write($e->getMessage());
            return $response->withStatus(404);
        }
    }
    //*Eliminar (l)
    public function delete($request, $response, $args){
        $db = new DB();
        try {
            if (!isset($args['id'])) throw new Exception("No se recibió el id", 400);
            if (!is_numeric($args['id'])) throw new Exception("El id debe ser numérico", 400);
            if (!$db->existsIn('juegos', $args['id'])) throw new Exception("No se encontró el id: '" . $args['id'] . "'", 404);
            $db->makeQuery("DELETE FROM juegos WHERE id = ?", [$args['id']]);
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
?>    
