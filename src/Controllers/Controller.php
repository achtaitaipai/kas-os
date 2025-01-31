<?php

namespace App\Controllers;

use App\Application\Settings;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Flash\Messages;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;

abstract class Controller
{
  public function __construct(protected Twig $view, protected Settings $settings, protected Messages $flash) {}

  protected function redirect(ServerRequestInterface $request, ResponseInterface $response, string $route, ?array $data = [], ?array $queryParams = [], ?int $code = 302): ResponseInterface
  {
    $routeParser = RouteContext::fromRequest($request)->getRouteParser();
    return $response->withHeader('Location', $routeParser->urlFor($route, $data, $queryParams))->withStatus($code);
  }
}
