<?php

declare(strict_types=1);

use App\Core\Router;
use App\Core\Security;
use App\Core\Session;

require __DIR__ . '/../src/bootstrap.php';

Security::sendHeaders();

Session::start();

$routes = require __DIR__ . '/../config/routes.php';
$router = new Router($routes);
$router->dispatch(
    $_SERVER['REQUEST_METHOD'],
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/'
);
