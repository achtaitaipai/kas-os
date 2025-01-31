<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class HomeController extends Controller
{

  public function show(ServerRequestInterface $request, ResponseInterface $response, array $args)
  {
    return $this->view->render($response, 'pages/home.html.twig');
  }
}
