<?php

// Enable error reporting for debugging purposes
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);


require_once '../vendor/autoload.php';


// Load Config

$config = require_once '../config/config.php';
// var_dump($config); // Config debuggen
// echo "Configuration loaded successfully.\n";

// Services
// echo "Loading services...\n";
require_once '../config/services.php';
// echo "Services loaded successfully.\n";

// Router
// echo "Loading router...\n";
$router = require_once '../routes/router.php';
if ($router) {
    // echo "Router loaded successfully.\n";
} else {
    // echo "Failed to load router.\n";
}

// Run application through router:
try {
    // echo "Running the router...\n";
    $router->run();
    // echo "Router ran successfully.\n";
} catch (\App\Plugins\Http\ApiException $e) {
    // Send the API exception to the client:
    // echo "Caught APIException: " . $e->getMessage() . "\n";
    $e->send();
} catch (Exception $e) {
    // For debugging purposes, throw the initial exception:
    // echo "Caught General Exception: " . $e->getMessage() . "\n";
    throw $e;
}

// echo "Application execution completed.\n";
