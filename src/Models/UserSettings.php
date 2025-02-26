<?php

namespace App\Models;

use Symfony\Component\Filesystem\Filesystem;

class UserSettings
{
  public static string $settingsPath = __DIR__ .  DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR  . '..' . DIRECTORY_SEPARATOR . 'settings.json';

  public array $data;

  public function __construct()
  {
    $fileSystem = new Filesystem();
    $this->data = json_decode($fileSystem->readFile(self::$settingsPath), true);
  }

  public function title(): string
  {
    return $this->data["title"];
  }
  public function metadesc(): string
  {
    return $this->data["metadesc"];
  }
  public function lang(): string
  {
    return $this->data["lang"];
  }
  public function backgroundColor(): string
  {
    return $this->data["backgroundColor"];
  }
  public function backgroundImage(): string
  {
    return $this->data["backgroundImage"];
  }
  public function repeat(): bool
  {
    return $this->data["repeat"];
  }
  public function cover(): bool
  {
    return $this->data["cover"];
  }

  public static function update(array $data)
  {
    $fileSystem = new Filesystem();
    $fileSystem->dumpFile(self::$settingsPath, json_encode($data));
  }
}
