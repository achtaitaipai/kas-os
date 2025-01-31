<?php

namespace App\Middlewares;

use App\Application\Auth;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Routing\RouteContext;

class AuthMiddleware
{
  public function __invoke(Request $request, RequestHandler $handler): Response
  {
    $auth = Auth::isLogged();
    if (!$auth) {
      $response = $handler->handle($request);
      $routeParser = RouteContext::fromRequest($request)->getRouteParser();
      return $response->withHeader('Location', $routeParser->urlFor("login"))->withStatus(302);
    }
    return $handler->handle($request);
  }
}
