<?php

namespace App\Application;

use App\Models\User;

class Auth
{

  private static string $sessionName = "auth";

  public static function login(string $email, string $password)
  {
    if (!User::verifyCredentials($email, $password)) return false;
    $_SESSION[self::$sessionName] = $email;
    return true;
  }

  public static function isLogged()
  {
    if (!isset($_SESSION[self::$sessionName]) || is_null(User::get($_SESSION[self::$sessionName]))) return false;
    return true;
  }

  public static function logout()
  {
    if (isset($_SESSION[self::$sessionName])) unset($_SESSION[self::$sessionName]);
  }
}
