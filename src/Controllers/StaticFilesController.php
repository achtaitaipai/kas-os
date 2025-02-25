<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;
use Slim\Psr7\Factory\StreamFactory;
use Symfony\Component\Finder\Finder;
use Twig\Extra\String\StringExtension;

class StaticFilesController extends Controller
{

  public function show(ServerRequestInterface $request, ResponseInterface $response, array $args)
  {
    $finder = new Finder();
    $finder->files()->in($this->settings->get("content_path"));
    $target = null;
    $slugger = new StringExtension();
    $fileSlug = $args['file'];
    foreach ($finder as $file) {
      $slug = $slugger->createSlug($file->getRelativePath() . DIRECTORY_SEPARATOR . $file->getFilename())->toString();
      if ($fileSlug === $slug) $target = $file->getRealPath();
    }
    if (is_null($target)) throw new HttpNotFoundException($request);
    $mimeType = mime_content_type($target);
    if (!str_starts_with($mimeType, "image/") && !str_starts_with($mimeType, "audio/"))
      throw new HttpNotFoundException($request);
    $streamFactory = new StreamFactory();
    $stream = $streamFactory->createStreamFromFile($target);
    return $response
      ->withHeader('Content-Type', $mimeType)
      ->withBody($stream);
  }
}
