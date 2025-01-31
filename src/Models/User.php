<?php

namespace App\Models;

use Symfony\Component\Filesystem\Filesystem;

class User
{
  private static $path = __DIR__ . '/../../acounts.txt';

  public function __construct(public string $email, private string $hash) {}

  public function verify(string $password): bool
  {
    return password_verify($password, $this->hash);
  }

  /**
   * @return User[]
   */
  public static function getAll(): array
  {
    $fileSystem = new Filesystem();
    if (!$fileSystem->exists(self::$path)) return [];
    $lines = file(self::$path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $users = [];
    foreach ($lines as $line) {
      list($email, $hash) = explode(':', $line, 2);
      $users[] = new User($email, $hash);
    }
    return $users;
  }

  public static function get(string $email): User | null
  {
    $users = self::getAll();
    foreach ($users as $user) {
      if ($email === $user->email) return $user;
    }
    return null;
  }

  public static function verifyCredentials(string $email, string $password): bool
  {
    $user = self::get($email);
    if (is_null($user)) return false;
    return $user->verify($password);
  }

  public static function create(string $email, string $password)
  {
    $fileSystem = new Filesystem();
    $data = $email . ':' . password_hash($password, PASSWORD_DEFAULT) . PHP_EOL;
    $fileSystem->appendToFile(self::$path, $data);
  }
}
