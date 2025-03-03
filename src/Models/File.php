<?php

namespace App\Models;

use Error;
use League\CommonMark\CommonMarkConverter;
use RuntimeException;
use Slim\Psr7\UploadedFile;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;
use Twig\Extra\String\StringExtension;

class File
{
  public static $dirPath = __DIR__ .  DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR  . '..' . DIRECTORY_SEPARATOR . 'content';

  private string $path;


  public function __construct(string $path)
  {
    $this->path = Path::canonicalize($path);
    return null;
  }

  public function path()
  {
    return $this->path;
  }

  public function name()
  {
    return  pathinfo($this->path, PATHINFO_FILENAME);
  }

  public function displayName()
  {
    $filename =  pathinfo($this->path, PATHINFO_FILENAME);
    return preg_replace('/^-?\d+_/', '', $filename);
  }

  public function order()
  {

    preg_match('/^(-?\d+)_/', $this->name(), $matches);
    if (empty($matches)) return null;
    return $matches[1];
  }

  public function baseName()
  {
    return pathinfo($this->path, PATHINFO_BASENAME);
  }

  public function extension()
  {
    return pathinfo($this->path, PATHINFO_EXTENSION);
  }

  public function time()
  {
    return filemtime($this->absolutePath());
  }

  private function absolutePath()
  {
    return self::$dirPath . DIRECTORY_SEPARATOR . $this->path;
  }

  public function directory()
  {
    $directory =  Path::getDirectory($this->path());
    if ($directory === $this->path()) return null;
    return $directory;
  }

  public function content()
  {
    if (! $this->isLink() && !$this->isMd()) return null;
    $filesystem = new Filesystem();
    return $filesystem->readFile($this->absolutePath());
  }

  public function childrens(?bool $withHidden = true)
  {
    if (!$this->isDir()) return null;
    $rawPathes = scandir($this->absolutePath());
    $files = [];
    foreach ($rawPathes as $path) {
      if (str_starts_with($path, ".")) continue;
      if (!$withHidden && str_starts_with($path, '_')) continue;
      $file = new File($this->path() . DIRECTORY_SEPARATOR . $path);
      $files[] = $file;
    }
    usort($files, function (File $a, File $b) {
      if ($a->isDir() && !$b->isDir()) return -1;
      else if ($b->isDir() && !$a->isDir()) return 1;
      if ($a->order() === $b->order())
        return $b->time() - $a->time();
      if (is_null($a->order())) return 1;
      if (is_null($b->order())) return -1;
      return $a->order() - $b->order();
    });
    return $files;
  }

  public function slug()
  {
    $slugger = new StringExtension();
    return   $slugger->createSlug($this->path())->toString();
  }

  public function isDir()
  {
    return $this->type() === "directory";
  }

  public function isImage()
  {
    return $this->type() === "image";
  }

  public function isMd()
  {
    return $this->type() === "markdown";
  }

  public function isLink()
  {
    return $this->type() === "link";
  }

  public function isAudio()
  {
    return $this->type() === "audio";
  }

  public function isUnknown()
  {
    return $this->type() === "unknown";
  }

  public function hidden()
  {
    return str_starts_with($this->name(), '_');
  }

  public function type()
  {
    $filesystem = new Filesystem();
    if (!$filesystem->exists($this->absolutePath())) {
      return null;
    }

    if (is_dir($this->absolutePath())) return "directory";

    $mimeType = mime_content_type($this->absolutePath());

    if (!$mimeType) {
      return 'unknown';
    }

    if (str_starts_with($mimeType, 'image/')) {
      return 'image';
    }
    if (str_starts_with($mimeType, 'audio/')) {
      return 'audio';
    }

    $extension = $this->extension();
    if (strtolower($extension) === "md") {
      return 'markdown';
    }
    if (strtolower($extension) === "url") {
      return 'link';
    }
    return 'unknown';
  }

  public function asArray(?bool $withDeep = true, ?bool $withHidden = false): array
  {
    $data =  [
      "type" => $this->type(),
      "parent" => $this->directory(),
      "path" => $this->path(),
      "name" => $this->displayName(),
    ];

    switch ($data["type"]) {
      case 'link':
        $data["url"] = $this->content();
        break;

      case 'directory':
        if ($withDeep)
          $data["childrens"] = array_map(fn($f) => $f->asArray(false), $this->childrens(withHidden: $withHidden));
        break;

      case 'markdown':
        if ($withDeep) {
          $converter = new CommonMarkConverter(["html_input" => false]);
          $data["content"] = $converter($this->content())->getContent();
        }
        break;

      default:
        $data["src"] = $this->slug();
        break;
    }

    return $data;
  }

  public static function isValidPath(string $path)
  {
    $filesystem = new Filesystem();
    $absolutePath = self::$dirPath . DIRECTORY_SEPARATOR . $path;
    if (!Path::isBasePath(self::$dirPath, $absolutePath)) return false;
    if (!$filesystem->exists($absolutePath)) return false;
    return true;
  }

  public static function exists(string $path)
  {
    $filesystem = new Filesystem();
    $absolutePath = self::$dirPath . DIRECTORY_SEPARATOR . $path;
    return $filesystem->exists($absolutePath);
  }

  public static function editContent(File $file, string $content)
  {
    if (!$file->isMd() && !$file->isLink()) return false;
    $fileSystem = new Filesystem();
    return $fileSystem->dumpFile($file->absolutePath(), $content);
  }

  public static function rename(File $file, string $newName)
  {
    $newPath = Path::canonicalize(self::$dirPath . DIRECTORY_SEPARATOR . $file->directory() . DIRECTORY_SEPARATOR . $newName);
    if (!$file->isDir()) $newPath .= "." . $file->extension();
    if (Path::getDirectory($newPath) !== Path::getDirectory($file->absolutePath())) throw new RuntimeException("Invalid name");
    $fileSystem = new Filesystem();
    return $fileSystem->rename($file->absolutePath(), $newPath);
  }

  public static function remove(File $file)
  {
    $filesystem = new Filesystem();
    return $filesystem->remove($file->absolutePath());
  }

  public static function upload(File $directory, UploadedFile $uploadedFile)
  {
    $newPath = Path::canonicalize($directory->absolutePath() . DIRECTORY_SEPARATOR . $uploadedFile->getClientFilename());
    $filesystem = new Filesystem();
    if ($filesystem->exists($newPath)) throw new Error("Upload failed: The file '" . $uploadedFile->getClientFilename() . "' could not be uploaded because a file with the same name already exists.");
    $uploadedFile->moveTo($newPath);
  }

  public static function makeDir(File $directory, string $name)
  {
    $path = Path::canonicalize($directory->absolutePath() . DIRECTORY_SEPARATOR . $name);
    $filesystem = new Filesystem();
    $filesystem->mkdir($path);
  }

  public static function makeFile(File $directory, string $name, string $content)
  {
    $path = Path::canonicalize($directory->absolutePath() . DIRECTORY_SEPARATOR . $name);
    $filesystem = new Filesystem();
    $filesystem->dumpFile($path, $content);
  }
}
