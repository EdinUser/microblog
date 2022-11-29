<?php
session_start();

// Init Slim
use Slim\App;
use Slim\Exception\MethodNotAllowedException;
use Slim\Exception\NotFoundException;
use Slim\Views\TwigExtension;
use Slim\Http\Environment;
use Slim\Http\Uri;
use Slim\Views\Twig;
use Twig\Extension\DebugExtension;
use Twig\TwigFilter;

use MicroBlog\Utils\Db;
use MicroBlog\Utils\Pagination;

$app = new App(
  [
    'settings' =>
      [
        'displayErrorDetails' => ($_ENV['ENV'] === 'DEV'),
      ],
  ]
);

// Init container
$container = $app->getContainer();

// Twig
// Register component on container
$container['view'] = function ($container) {
    $view = new Twig($_ENV['ROOT_FOLDER'] . '/core/Views', [
      'cache' => false,
      'debug' => ($_ENV['ENV'] === 'DEV'),
    ]);

    $router = $container->get('router');
    $uri = Uri::createFromEnvironment(new Environment($_SERVER));
    $view->addExtension(new TwigExtension($router, $uri));
    $view->addExtension(new DebugExtension());

    // Custom filter to shorten the content
    $function = new TwigFilter('shorten', function ($text, $length) {
        $shortText = trim(strip_tags($text));
        $shortText = preg_replace("/<(.*?)>/s", "", $shortText);
        $shortText = preg_replace("/\[(.*?)\]/s", "", $shortText);

        $shortText = preg_replace("/^(.{".$length."}).*$/s", "\\1", $shortText);

        return preg_replace("/ [^ ]*$/s", "...", $shortText);
        // ...
    });
    $view->getEnvironment()->addFilter($function);

    // add user logged check for Twig usage
    $view->getEnvironment()->addGlobal('is_logged', isset($_SESSION['siteid']));
    $view->getEnvironment()->addGlobal('is_admin', isset($_SESSION['is_admin']));

    return $view;
};

$container['db'] = function () {
    return new Db();
};

$container['pagination'] = function ($container) {
    return new Pagination($container);
};

// Routes
require __DIR__ . '/routes.php';

// Run, Forest, run!
try {
    $app->run();
}
catch (MethodNotAllowedException $e) {
    echo 'Method not allowed!';
}
catch (NotFoundException $e) {
    echo '404';
}
catch (Exception $e) {
    echo 'Error!' . $e->getMessage();
}
catch (Throwable $e) {
    echo $e->getMessage();
}