<?php

use App\Plugins\Di\Container;

// Retrieve the router from the container
$router = Container::getInstance()->getShared('router');

// Set base path for the router
$router->setBasePath('/web_backend_test_catering_api');

// Require routes
require_once '../routes/routes.php';

// Handle 404 errors
$router->set404(function () {
    throw new \App\Plugins\Http\Exceptions\NotFound(['error' => 'Route not defined']);
});

// Return the router instance
return $router;
