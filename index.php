<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
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
$app->get('/juegos', '\App\src\Models\PlataformaController:juegos');
$app->run();