<?php

namespace App\Controllers;

use App\Application\Auth;
use App\Models\User;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validator as v;

class AuthController extends Controller
{
  public function create(ServerRequestInterface $request, ResponseInterface $response)
  {
    if (count(User::getAll()) > 0)
      return $this->redirect($request, $response, "login");
    return $this->view->render($response, "pages/signup.html.twig");
  }

  public function store(ServerRequestInterface $request, ResponseInterface $response)
  {
    if (count(User::getAll()) > 0)
      return $this->redirect($request, $response, "login");
    $data = $request->getParsedBody();
    $validator = v::attribute('email', v::stringType()->length(1, 32))
      ->attribute('password', v::StringType()->length(8, 64));
    $errors = [];
    try {
      $validator->assert((object) $data);
    } catch (NestedValidationException $e) {
      $errors = $e->getMessages();
    }
    if (!empty($errors)) return $this->view->render($response, "pages/signup.html.twig", ["errors" => $errors, "data" => $data]);
    User::create($data["email"], $data["password"]);
    $this->flash->addMessage("success", "Your account has been successfully created.");
    return $this->redirect($request, $response, "login");
  }

  public function login(ServerRequestInterface $request, ResponseInterface $response)
  {
    if (count(User::getAll()) === 0)
      return $this->redirect($request, $response, "signup");
    return $this->view->render($response, "pages/login.html.twig");
  }
  public function doLogin(ServerRequestInterface $request, ResponseInterface $response)
  {
    $data = $request->getParsedBody();
    $validator = v::attribute('email', v::stringType()->length(1, 32))
      ->attribute('password', v::StringType());
    $errors = [];
    try {
      $validator->assert((object) $data);
    } catch (NestedValidationException $e) {
      $errors = $e->getMessages();
    }
    if (!Auth::login($data["email"], $data["password"])) $errors["email"] = "invalid credentials";
    if (!empty($errors)) return $this->view->render($response, "pages/login.html.twig", ["errors" => $errors, "data" => $data]);
    return $this->redirect($request, $response, "files");
  }
  public function logout(ServerRequestInterface $request, ResponseInterface $response)
  {
    Auth::logout();
    return $this->redirect($request, $response, "home");
  }
}
