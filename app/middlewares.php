<?php

use DI\Container;
use Slim\App;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;

return function (App $app, Container $container) {
  $app->add(TwigMiddleware::create($app, $container->get(Twig::class)));
  $app->addRoutingMiddleware();
  $app->addErrorMiddleware(true, true, true);
};
