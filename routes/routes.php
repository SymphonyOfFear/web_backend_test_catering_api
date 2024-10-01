<?php

use App\Controllers\TagController;
use App\Controllers\AuthController;
use App\Controllers\IndexController;
use App\Controllers\FacilityController;
use App\Controllers\LocationController;
use Bramus\Router\Router; // Zorg ervoor dat de Router klasse correct is geïmporteerd

/** @var Router $router */

// Definieer routes
$router->get('/test', IndexController::class . '@test');
$router->get('/', IndexController::class . '@test');

// Authenticatie routes
$router->get('/auth/login', AuthController::class . '@login');
$router->post('/auth/login', AuthController::class . '@login');
$router->get('/auth/register', AuthController::class . '@register');
$router->post('/auth/register', AuthController::class . '@register');
$router->get('/auth/logout', AuthController::class . '@logout');

// Facility routes: Eerst de search route plaatsen
$router->get('/facility/search', FacilityController::class . '@searchFacilityByName'); // Plaats de zoekroute boven de ID route

// Daarna de routes die een ID gebruiken
$router->post('/facility', FacilityController::class . '@addFacility');
$router->get('/facility', FacilityController::class . '@listFacilities');
$router->get('/facility/{id}', FacilityController::class . '@getFacilityById');
$router->put('/facility/{id}', FacilityController::class . '@editFacility');
$router->delete('/facility/{id}', FacilityController::class . '@removeFacility');

// Tag routes
$router->post('/tag', TagController::class . '@create');
$router->get('/tag', TagController::class . '@getAll');

// Locatie routes
$router->post('/location', LocationController::class . '@create');
$router->get('/location', LocationController::class . '@getAll');
