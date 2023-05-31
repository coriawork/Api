<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require 'App/src/Models/GenerosController.php';
require 'App/src/Models/PlataformaController.php';
require 'App/src/Models/JuegosController.php';
require 'vendor/autoload.php';
require 'App/src/Models/DB.php';

$app = AppFactory::create();

//endpoints Generos
$app->post('/generos', '\App\src\Models\GenerosController:create');
$app->get('/generos','\App\src\Models\GenerosController:list');
$app->put('/generos/{id}', '\App\src\Models\GenerosController:update');
$app->delete('/generos', '\App\src\Models\GenerosController:delete');

//endpoints Plataformas
$app->post('/plataformas', '\App\src\Models\PlataformaController:create');
$app->get('/plataformas', '\App\src\Models\PlataformaController:list');
$app->put('/plataformas/{id}', '\App\src\Models\PlataformaController:update');
$app->delete('/plataformas/{id}', '\App\src\Models\PlataformaController:delete');
//endpoints Juegos

/*
i) Crear un nuevo juego: implementar un endpoint para crear un nuevo
juego en la tabla de juegos. El endpoint debe permitir enviar el nombre,
imagen, descripción, plataforma, URL y género.
*/
$app->post('/juegos', '\App\src\Models\JuegosController:create');

/*j) Actualizar información de un juego: implementar un endpoint para
actualizar la información de un juego existente en la tabla de juegos. El
endpoint debe permitir enviar el id y los campos que se quieran
actualizar*/
$app->put('/juegos/{id}', '\App\src\Models\JuegosController:update');

/*k) Eliminar un juego: el endpoint debe permitir enviar el id del juego y
eliminarlo de la tabla.*/

$app->delete('/juegos/{id}', '\App\src\Models\JuegosController:delete');

/*l) Obtener todos los juegos: implemente un endpoint para obtener todos
los juegos de la tabla.*/
$app->get('/juegos', '\App\src\Models\JuegosController:list');

/* m) Buscar juegos: implementar un endpoint que permita buscar juegos por
nombre, plataforma y género. El endpoint deberá aceptar un nombre, un
id de género, un id de plataforma y un orden por nombre (ASC o DESC)*/

$app->get('/juegos{nombre,id_genero,id_plataforma,orden}', '\App\src\Models\JuegosController:list');

//
$app->run();
