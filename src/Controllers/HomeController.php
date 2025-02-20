<?php

namespace App\Controllers;

use App\Application\Settings;
use App\Models\UserSettings;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class HomeController extends Controller
{

  public function show(ServerRequestInterface $request, ResponseInterface $response, array $args)
  {
    $settings = new UserSettings();
    return $this->view->render($response, 'pages/home.html.twig', ["settings" => $settings]);
  }
}
