<?php

use DI\Container;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

session_start();

$container = new Container();

$dependencies = include __DIR__ . "/../app/dependencies.php";
$dependencies($container);

$app = AppFactory::createFromContainer($container);

$middlewares = include __DIR__ . "/../app/middlewares.php";
$middlewares($app, $container);

$routes = include __DIR__ . "/../app/routes.php";
$routes($app);

$app->run();
