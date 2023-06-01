<?php
use Slim\Factory\AppFactory;

require 'App/src/Models/GenerosController.php';
require 'App/src/Models/PlataformaController.php';
require 'vendor/autoload.php';
require 'App/src/Models/DB.php';

$app = AppFactory::create();

//endopoints Generos
$app->post('/generos', '\App\src\Models\GenerosController:create');
$app->get('/generos','\App\src\Models\GenerosController:list');
$app->put('/generos/{id}', '\App\src\Models\GenerosController:update');
$app->delete('/generos', '\App\src\Models\GenerosController:delete');

//endopoints Plataformas
$app->post('/plataformas', '\App\src\Models\PlataformaController:create');
$app->get('/plataformas', '\App\src\Models\PlataformaController:list');
$app->put('/plataformas/{id}', '\App\src\Models\PlataformaController:update');
$app->delete('/plataformas/{id}', '\App\src\Models\PlataformaController:delete');
//pruebaJuegos

$app->get('/juegosall', '\App\src\Models\PlataformaController:juegosAll');
/* m) Buscar juegos: implementar un endpoint que permita buscar juegos por nombre, plataforma y género. 
El endpoint deberá aceptar un nombre, un id de género, un id de plataforma y un orden por nombre (ASC o DESC)*/

$app->get('/juegos', '\App\src\Models\PlataformaController:juegos');
//endpoints Juegos

/*
i) Crear un nuevo juego: implementar un endpoint para crear un nuevo juego en la tabla de juegos.
El endpoint debe permitir enviar el nombre, imagen, descripción, plataforma, URL y género.
*/
$app->post('/juegos', '\App\src\Models\PlataformaController:createJuego');
/*j) Actualizar información de un juego: implementar un endpoint para actualizar la información de un juego
existente en la tabla de juegos. El endpoint debe permitir enviar el id y los campos que se quieran actualizar*/
$app->put('/juegos/{id}', '\App\src\Models\PlataformaController:updateJuegos');

// Correr la aplicación
$app->run();