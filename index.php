<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
require 'App/src/GenerosController.php';

require 'vendor/autoload.php';
require 'App/src/DB.php';
$app = AppFactory::create();

$app->get('/generos', function(Request $request, Response $response, $args){
    $db = mysqli_connect('localhost', 'root', '','pagjuego');
    $generos = $db->query('SELECT * FROM generos')->fetch_all(MYSQLI_ASSOC);
    $response->getBody()->write(json_encode($generos));
    return $response;
});
/* $app->get('/generos/get/{genero}','\App\src\GenerosController:getGen'); */

$app->get('/generos/list','\App\src\GenerosController:list');

$app->post('/generos', '\App\src\GenerosController:create');



$app->run();