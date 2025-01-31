<?php

use App\Application\Auth;
use App\Application\Settings;
use App\Application\Vite;
use DI\Container;
use Slim\Flash\Messages;
use Slim\Views\Twig;
use Symfony\Component\Filesystem\Filesystem;
use Twig\Extension\DebugExtension;
use Twig\Extra\Markdown\DefaultMarkdown;
use Twig\Extra\Markdown\MarkdownExtension;
use Twig\Extra\Markdown\MarkdownRuntime;
use Twig\Extra\String\StringExtension;
use Twig\RuntimeLoader\RuntimeLoaderInterface;
use Twig\TwigFunction;

return function (Container $container) {
  $container->set(Settings::class, function () {
    $data = include __DIR__ . DIRECTORY_SEPARATOR . "settings.php";
    return new Settings($data);
  });

  $container->set(Messages::class, function () {
    return new Messages();
  });

  $container->set(Twig::class, function (Container $container) {
    $flash = $container->get(Messages::class);
    $twig =  Twig::create(__DIR__ . '/../src/templates', ['cache' => false]);
    $twig->getEnvironment()->enableDebug();
    $twig->getEnvironment()->addFunction(new TwigFunction('vite', function (string $entry) {
      return Vite::links($entry);
    }));
    $twig->getEnvironment()->addFunction(new TwigFunction('flashMessages', function () use ($flash) {
      return $flash->getMessages();
    }));
    $twig->addExtension(new DebugExtension);
    $twig->addExtension(new  StringExtension());
    $twig->addExtension(new MarkdownExtension());
    $twig->addRuntimeLoader(new class implements RuntimeLoaderInterface {
      public function load($class)
      {
        if (MarkdownRuntime::class === $class) {
          return new MarkdownRuntime(new DefaultMarkdown());
        }
      }
    });
    $twig->getEnvironment()->addFunction(new TwigFunction('isLogged', function () {
      return Auth::isLogged();
    }));
    return $twig;
  });

  $container->set(Filesystem::class, function () {
    return new Filesystem();
  });
};
