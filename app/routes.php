<?php

use App\Controllers\AuthController;
use App\Controllers\FileController;
use App\Controllers\HomeController;
use App\Controllers\StaticFilesController;
use App\Middlewares\AuthMiddleware;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

return function (App $app) {
  $app->get('/', [HomeController::class, 'show'])->setName("home");
  $app->get('/signup', [AuthController::class, 'create'])->setName("signup");
  $app->post('/signup', [AuthController::class, 'store'])->setName("doSignup");
  $app->get('/login', [AuthController::class, 'login'])->setName("login");
  $app->post('/login', [AuthController::class, 'doLogin'])->setName("doLogin");
  $app->post('/logout', [AuthController::class, 'logout'])->setName("logout");

  $app->get('/api', [FileController::class, 'api']);

  $app->group("/admin", function (RouteCollectorProxy $group) {
    $group->get("", [FileController::class, "index"])->setName("files");
    $group->get("/show", [FileController::class, "show"])->setName("filesShow");
    $group->get("/edit", [FileController::class, "edit"])->setName("filesEdit");
    $group->post("/edit", [FileController::class, "update"])->setName("filesEdit");
    $group->post("/rename", [FileController::class, "rename"])->setName("filesRename");
    $group->post("/delete", [FileController::class, "delete"])->setName("filesDelete");
    $group->get("/upload", [FileController::class, "upload"])->setName("filesUpload");
    $group->post("/upload", [FileController::class, "doUpload"])->setName("filesUpload");
    $group->get("/create", [FileController::class, "create"])->setName("filesCreate");
    $group->post("/create", [FileController::class, "store"])->setName("filesCreate");
  })->add(new AuthMiddleware());

  $app->get("/static/{file}", [StaticFilesController::class, "show"])->setName("static");
};
