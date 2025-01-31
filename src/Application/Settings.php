<?php

namespace App\Application;

class Settings
{
  public function __construct(private array $data) {}

  public function has(string $key)
  {
    return isset($this->data[$key]);
  }

  public function get(string $key)
  {
    return $this->data[$key] ?? null;
  }
}
