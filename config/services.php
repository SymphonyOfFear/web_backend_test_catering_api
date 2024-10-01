<?php

use App\Plugins\Db\Db;
use App\Plugins\Di\Container;
use Bramus\Router\Router;

// Load the configuration file
$config = require __DIR__ . '/config.php';

// Retrieve the database configuration values
$dbConfig = $config['db'];

// Create and configure the Db instance using configuration values
Container::getInstance()->setShared('db', function () use ($dbConfig) {
    return new Db(
        $dbConfig['host'],
        $dbConfig['database'],
        $dbConfig['username'],
        $dbConfig['password']
    );
});

// Register the router as a shared service
Container::getInstance()->setShared('router', function () {
    return new Router();
});
