<?php

/*https://github.com/andrefelipe/vite-php-setup/tree/master*/

namespace App\Application;

use Twig\Extension\AbstractExtension;


class Vite extends AbstractExtension
{
  private static $host = "http://localhost:5133";

  static function links(string $entry)
  {
    return "\n" . self::jsTag($entry)
      . "\n" . self::jsPreloadImports($entry)
      . "\n" . self::cssTag($entry);
  }

  private static function isDev(string $entry): bool
  {
    static $exists = null;
    if ($exists !== null) {
      return $exists;
    }
    $handle = curl_init(self::$host . '/' . $entry);
    curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($handle, CURLOPT_NOBODY, true);

    curl_exec($handle);
    $error = curl_errno($handle);
    curl_close($handle);

    return $exists = !$error;
  }

  private static function jsTag(string $entry): string
  {
    $url = self::isDev($entry)
      ? self::$host . '/' . $entry
      : self::assetUrl($entry);

    if (!$url) {
      return '';
    }
    if (self::isDev($entry)) {
      return '<script type="module" src="' . self::$host . '/@vite/client"></script>' . "\n"
        . '<script type="module" src="' . $url . '"></script>';
    }
    return '<script type="module" src="' . $url . '"></script>';
  }

  private static function jsPreloadImports(string $entry): string
  {
    if (self::isDev($entry)) {
      return '';
    }

    $res = '';
    foreach (self::importsUrls($entry) as $url) {
      $res .= '<link rel="modulepreload" href="'
        . $url
        . '">';
    }
    return $res;
  }

  private static function cssTag(string $entry): string
  {
    if (self::isDev($entry)) {
      return '';
    }

    $tags = '';
    foreach (self::cssUrls($entry) as $url) {
      $tags .= '<link rel="stylesheet" href="'
        . $url
        . '">';
    }
    return $tags;
  }


  private static function getManifest(): array
  {
    $content = file_get_contents(__DIR__ . '/../../public/dist/.vite/manifest.json');
    return json_decode($content, true);
  }

  private static function assetUrl(string $entry): string
  {
    $manifest = self::getManifest();

    return isset($manifest[$entry])
      ? '/dist/' . $manifest[$entry]['file']
      : '';
  }

  private static function importsUrls(string $entry): array
  {
    $urls = [];
    $manifest = self::getManifest();

    if (!empty($manifest[$entry]['imports'])) {
      foreach ($manifest[$entry]['imports'] as $imports) {
        $urls[] = '/dist/' . $manifest[$imports]['file'];
      }
    }
    return $urls;
  }

  private static function cssUrls(string $entry): array
  {
    $urls = [];
    $manifest = self::getManifest();

    if (!empty($manifest[$entry]['css'])) {
      foreach ($manifest[$entry]['css'] as $file) {
        $urls[] = '/dist/' . $file;
      }
    }
    return $urls;
  }
}
