<?php

use Symfony\Component\Filesystem\Path;

return [
  "content_path" => Path::canonicalize(__DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "content")
];
