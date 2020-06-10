<?php

include "vendor/autoload.php";

use MiladRahimi\PhpRouter\Router;
use App\Middleware\AuthMiddleware;

$router = new Router();

$router->get('/', 'App\Controllers\EPGController@show', AuthMiddleware::class);

$router->dispatch();

