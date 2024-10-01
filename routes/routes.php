<?php

use App\Controllers\TagController;
use App\Controllers\AuthController;
use App\Controllers\IndexController;
use App\Controllers\FacilityController;
use App\Controllers\LocationController;

/** @var Bramus\Router\Router $router */

// Define routes here
$router->get('/test', IndexController::class . '@test');
$router->get('/', IndexController::class . '@test');

// Authentication routes
$router->get('/auth/login', AuthController::class . '@login');
$router->post('/auth/login', AuthController::class . '@login');
$router->get('/auth/register', AuthController::class . '@register');
$router->post('/auth/register', AuthController::class . '@register');
$router->get('/auth/logout', AuthController::class . '@logout');

// Facility routes
$router->post('/facility', FacilityController::class . '@create');
$router->get('/facility', FacilityController::class . '@readAll'); // Renamed for clarity
$router->get('/facility/{id}', FacilityController::class . '@read'); // Read by ID
$router->put('/facility/{id}', FacilityController::class . '@update');
$router->delete('/facility/{id}', FacilityController::class . '@delete');
$router->get('/facility/search/{query}', FacilityController::class . '@search'); // Search facilities

// Tag routes
$router->post('/tag', TagController::class . '@create');
$router->get('/tag', TagController::class . '@getAll');

// Location routes
$router->post('/location', LocationController::class . '@create');
$router->get('/location', LocationController::class . '@getAll');
