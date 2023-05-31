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
$app->post('/juegos', '\App\src\Models\JuegosController:create');
$app->get('/juegos', '\App\src\Models\JuegosController:list');
$app->put('/juegos/{id}', '\App\src\Models\JuegosController:update');
$app->delete('/juegos/{id}', '\App\src\Models\JuegosController:delete');

//
$app->run();
