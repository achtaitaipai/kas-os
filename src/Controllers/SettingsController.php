<?php

namespace App\Controllers;

use App\Models\UserSettings;
use Respect\Validation\Validator as v;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Respect\Validation\Exceptions\NestedValidationException;

class SettingsController extends Controller
{
  public function edit(ServerRequestInterface $request, ResponseInterface $response)
  {
    $settings = new UserSettings();
    return $this->view->render($response, "pages/settings.html.twig", ["settings" => $settings]);
  }

  public function update(ServerRequestInterface $request, ResponseInterface $response)
  {
    $data = $request->getParsedBody();
    $validator = v::attribute(
      'title',
      v::stringType()
        ->length(1, 100)
        ->setTemplate('The title must be a non-empty string (1-100 characters).')
    )->attribute(
      'lang',
      v::regex('/^[a-z]{2}$/i')
        ->setTemplate('The lang must be a 2-letter code.'),
      true
    )->attribute(
      'backgroundColor',
      v::hexRgbColor(),

    )->attribute(
      'backgroundImage',
      v::optional(
        v::oneOf(
          v::url(),
          v::regex('/^[\/.\w\-~]+$/')
        )
      )->setTemplate('The backgroundImage must be a valid URL or local path.'),
      false
    )->attribute(
      'repeat',
      v::optional(
        v::equals('on')
          ->setTemplate('The repeat checkbox must be "on" if present.')
      ),
      false
    )->attribute(
      'cover',
      v::optional(
        v::equals('on')
          ->setTemplate('The cover checkbox must be "on" if present.')
      ),
      false
    );
    try {
      $validator->assert((object) $data);
      $userSettings = [
        "title" => $data["title"],
        "lang" => $data["lang"],
        "backgroundColor" => $data["backgroundColor"],
        "backgroundImage" => isset($data["backgroundImage"]) ? $data["backgroundImage"] : null,
        "repeat" => isset($data["repeat"]) && $data["repeat"] === "on" ? true : false,
        "cover" => isset($data["cover"]) && $data["cover"] === "on" ? true : false,
      ];
      UserSettings::update($userSettings);
      $this->flash->addMessage("success", "The settings has been successfully modified.");
      return $this->redirect($request, $response, "files");
    } catch (NestedValidationException $e) {
      $errors = $e->getMessages();
      return $this->view->render($response, "pages/settings.html.twig", ["data" => $data, "errors" => $errors]);
    }
  }
}
