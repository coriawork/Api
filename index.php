<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require 'App/src/Models/GenerosController.php';
require 'vendor/autoload.php';
require 'App/src/Models/DB.php';

$app = AppFactory::create();

/* $app->get('/generos/get/{genero}','\App\src\GenerosController:getGen'); */

$app->get('/generos/list','\App\src\GenerosController:list');

$app->post('/generos', '\App\src\GenerosController:create');

$app->put('/generos', '\App\src\GenerosController:update');

$app->run();