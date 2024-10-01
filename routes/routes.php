<?php

use App\Controllers\AuthController;
use App\Controllers\IndexController;

/** @var Bramus\Router\Router $router */

// Define routes here
$router->get('/test', IndexController::class . '@test');
$router->get('/', IndexController::class . '@test');

$router->get('/auth/login', AuthController::class . '@login');
$router->post('/auth/login', AuthController::class . '@login');

$router->get('/auth/register', AuthController::class . '@register');
$router->post('/auth/register', AuthController::class . '@register');

$router->get('/auth/logout', AuthController::class . '@logout');
