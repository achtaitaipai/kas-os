<?php

use DI\Container;
use Slim\App;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;

return function (App $app, Container $container) {
  $app->add(TwigMiddleware::create($app, $container->get(Twig::class)));
  $app->addRoutingMiddleware();
  $isDev = str_starts_with($_SERVER["HTTP_HOST"], "localhost");
  $app->addErrorMiddleware($isDev, $isDev, $isDev);
};
