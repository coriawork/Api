<?php
use Slim\Factory\AppFactory;

require 'App/src/Models/GenerosController.php';
require 'App/src/Models/PlataformaController.php';
require 'App/src/Models/JuegosController.php';
require 'vendor/autoload.php';
require 'App/src/Models/DB.php';

$app = AppFactory::create();

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: PUT,POST,DELETE,GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

//endpoints Generos (ver -> README.md)
$app->post('/generos', '\App\src\Models\GenerosController:create');
$app->get('/generos','\App\src\Models\GenerosController:list');
$app->put('/generos/{id}', '\App\src\Models\GenerosController:update');
$app->delete('/generos/{id}', '\App\src\Models\GenerosController:delete');

//endpoints Plataformas (ver -> README.md)
$app->post('/plataformas', '\App\src\Models\PlataformasController:create');
$app->get('/plataformas', '\App\src\Models\PlataformasController:list');
$app->put('/plataformas/{id}', '\App\src\Models\PlataformasController:update');
$app->delete('/plataformas/{id}', '\App\src\Models\PlataformasController:delete');

//endpoints Juegos (ver -> README.md)
$app->get('/juegosAll', '\App\src\Models\JuegosController:juegosAll');

$app->get('/juegos', '\App\src\Models\JuegosController:juegos');

$app->post('/juegos', '\App\src\Models\JuegosController:createJuego');

$app->put('/juegos/{id}', '\App\src\Models\JuegosController:updateJuegos');

$app->delete('/juegos/{id}', '\App\src\Models\JuegosController:delete');
// Correr la aplicación
$app->run();