<?php

namespace App\Controllers;

use App\Application\Settings;
use App\Models\File;
use Error;
use Respect\Validation\Validator as v;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Respect\Validation\Exceptions\NestedValidationException;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpNotFoundException;
use Slim\Flash\Messages;
use Slim\Views\Twig;
use Symfony\Component\Filesystem\Filesystem;

class FileController extends Controller
{

  public function __construct(Twig $view, Settings $settings, Messages $flash, private Filesystem $filesystem)
  {
    parent::__construct($view, $settings, $flash);
  }

  public function index(ServerRequestInterface $request, ResponseInterface $response)
  {
    $path = $request->getQueryParams()["path"] ?? "/";
    if (!File::isValidPath($path)) throw new HttpNotFoundException($request);
    $directory = new File($path);
    if (!$directory->isDir()) throw new HttpNotFoundException($request);
    return $this->view->render($response, "pages/files/index.html.twig", ["directory" => $directory]);
  }

  public function show(ServerRequestInterface $request, ResponseInterface $response)
  {
    $path = $request->getQueryParams()["path"];
    if (!isset($path) || !File::isValidPath($path)) throw new HttpNotFoundException($request);
    $file = new File($path);
    if ($file->isDir()) throw new HttpBadRequestException($request);
    return $this->view->render($response, "pages/files/show.html.twig", ["file" => $file]);
  }

  public function edit(ServerRequestInterface $request, ResponseInterface $response)
  {
    $path = $request->getQueryParams()["path"] ?? "/";
    if (!isset($path) || !File::isValidPath($path)) throw new HttpNotFoundException($request);
    $file = new File($path);
    return $this->view->render($response, "pages/files/edit.html.twig", ["file" => $file]);
  }

  public function update(ServerRequestInterface $request, ResponseInterface $response)
  {
    $data = $request->getParsedBody();
    $path = $data["path"];
    if (!isset($path) || !File::isValidPath($path)) throw new HttpBadRequestException($request);
    $file = new File($path);
    if ($file->isLink()) {
      $validator = v::attribute('url', v::url());
      try {
        $validator->assert((object) $data);
        File::editContent($file, $data["url"]);
        $this->flash->addMessage("success", "The file '" . $file->baseName() . "' has been successfully modified.");
        return $this->redirect($request, $response, "files", queryParams: ["path" => $file->directory()]);
      } catch (NestedValidationException $e) {
        $errors = $e->getMessages();
        return $this->view->render($response, "pages/files/edit.html.twig", ["file" => $file, "data" => $data, "errors" => $errors]);
      }
    } else if ($file->isMd()) {
      $validator = v::attribute('content', v::stringType());
      try {
        $validator->assert((object) $data);
        File::editContent($file, $data["content"]);
        $this->flash->addMessage("success", "The file '" . $file->baseName() . "' has been successfully modified.");
        return $this->redirect($request, $response, "files", queryParams: ["path" => $file->directory()]);
      } catch (NestedValidationException $e) {
        $errors = $e->getMessages();
        return $this->view->render($response, "pages/files/edit.html.twig", ["file" => $file, "data" => $data, "errors" => $errors]);
      }
    }
    throw new HttpBadRequestException($request);
  }

  public function rename(ServerRequestInterface $request, ResponseInterface $response)
  {
    $data = $request->getParsedBody();
    $path = $data["path"];
    if (!isset($path) || !File::isValidPath($path)) throw new HttpBadRequestException($request);
    $file = new File($path);
    $validator = v::attribute(
      'name',
      v::regex('/^[^\/\\\\:*?"<>|.]+$/')
        ->length(1, 255)
        ->setTemplate('The name is invalid. It must not contain an extension, special characters, or directory separators.')
    );
    $errors = [];
    try {
      $validator->assert((object) $data);
    } catch (NestedValidationException $e) {
      $errors = $e->getMessages();
    }
    if (empty($errors)) {
      $newPath = File::$dirPath . DIRECTORY_SEPARATOR . $file->directory() . $data["name"] . (empty($file->extension()) ? "" : "." . $file->extension());
      if ($this->filesystem->exists($newPath)) $errors["name"] = "a file with the same name already exists.";
    }
    if (!empty($errors)) {
      return $this->view->render($response, "pages/files/edit.html.twig", ["data" => $data, "file" => $file, "errors" => $errors]);
    }
    File::rename($file, $data["name"]);
    $this->flash->addMessage("success", "The item has been successfully renamed.");
    return $this->redirect($request, $response, "files", queryParams: ["path" => $file->directory()]);
  }

  public function delete(ServerRequestInterface $request, ResponseInterface $response)
  {
    $data = $request->getParsedBody();
    $path = $data["path"];
    if (!isset($path) || !File::isValidPath($path)) throw new HttpBadRequestException($request);
    $file = new File($path);
    File::remove($file);
    $this->flash->addMessage("success", "The item has been successfully deleted.");
    return $this->redirect($request, $response, "files", queryParams: ["path" => $file->directory()]);
  }

  public function upload(ServerRequestInterface $request, ResponseInterface $response)
  {
    $path = $request->getQueryParams()["path"];
    if (!isset($path) || !File::isValidPath($path)) throw new HttpNotFoundException($request);
    $directory = new File($path);
    if (!$directory->isDir()) $file = new File($directory->directory());
    return $this->view->render($response, "pages/files/upload.html.twig", ["directory" => $directory]);
  }

  public function doUpload(ServerRequestInterface $request, ResponseInterface $response)
  {
    $data = $request->getParsedBody();
    $path = $data["path"];
    if (!isset($path) || !File::isValidPath($path)) throw new HttpBadRequestException($request);
    $directory = new File($path);
    if (!$directory->isDir()) throw new HttpBadRequestException($request);
    $files = $request->getUploadedFiles()["files"];
    $validator = v::arrayType()->each(v::size(null, '1GB'));
    $errors = [];
    try {
      $validator->assert($files);
    } catch (NestedValidationException $e) {
      $errors["files[]"] = "the size of the files should not be greater than 1GB";
    }
    if (!empty($errors))
      return $this->view->render($response, "pages/files/upload.html.twig", ["directory" => $directory, "errors" => $errors]);
    foreach ($files as $uploadFile) {
      try {
        File::upload($directory, $uploadFile);
        $this->flash->addMessage("success", "The file '" . $uploadFile->getClientFilename() . "' have been successfully uploaded.");
      } catch (Error $e) {
        $this->flash->addMessage("error", $e->getMessage());
      }
    }
    return $this->redirect($request, $response, "files", queryParams: ["path" => $directory->path()]);
  }

  public function create(ServerRequestInterface $request, ResponseInterface $response)
  {
    $data = $request->getQueryParams();
    $path = $data["path"];
    if (!isset($path) || !File::isValidPath($path)) throw new HttpBadRequestException($request);
    $directory = new File($path);
    if (!$directory->isDir()) throw new HttpBadRequestException($request);
    $type = $data["type"];
    if (!isset($type) || ($type !== "markdown" && $type !== "link" && $type !== "directory")) throw new HttpBadRequestException($request);
    return $this->view->render($response, "pages/files/create.html.twig", ["directory" => $directory, "type" => $type]);
  }

  public function store(ServerRequestInterface $request, ResponseInterface $response)
  {
    $data = $request->getParsedBody();
    $path = $data["path"];
    if (!isset($path) || !File::isValidPath($path)) throw new HttpBadRequestException($request);
    $directory = new File($path);
    if (!$directory->isDir()) throw new HttpBadRequestException($request);
    $validator = v::attribute(
      'name',
      v::regex('/^[^\/\\\\:*?"<>|.]+$/')
        ->length(1, 255)
        ->setTemplate('The name is invalid. It must not contain an extension, special characters, or directory separators.')
    )->attribute(
      'type',
      v::in(["directory", "link", "markdown"])
    );
    if ($data["type"] === "link") $validator = $validator->attribute('content', v::url());
    if ($data["type"] === "markdown") $validator = $validator->attribute('content', v::stringType());
    $errors = [];
    try {
      $validator->assert((object) $data);
    } catch (NestedValidationException $e) {
      $errors = $e->getMessages();
    }
    $extension = "";
    if ($data["type"] === "markdown") $extension = ".md";
    if ($data["type"] === "link") $extension = ".url";
    if (File::exists($directory->path() . DIRECTORY_SEPARATOR . $data["name"] . $extension))
      $errors["name"] = "This name is already taken";
    if (!empty($errors))
      return $this->view->render($response, "pages/files/create.html.twig", ["directory" => $directory, "type" => $data["type"], "errors" => $errors, "data" => $data]);
    if ($data["type"] === "directory") {
      File::makeDir($directory, $data["name"]);
      $this->flash->addMessage("success", "The directory '" . $data["name"] . "' has been successfully created");
      return $this->redirect($request, $response, "files", [], ["path" => $directory->path()]);
    }
    File::makeFile($directory, $data["name"] . $extension, $data["content"]);
    $this->flash->addMessage("success", "The directory '" . $data["name"] . $extension . "' has been successfully created");
    return $this->redirect($request, $response, "files", [], ["path" => $directory->path()]);
  }
}
