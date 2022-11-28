<?php
session_start();

// Init Slim
use MicroBlog\Utils\Db;
use Slim\App;
use Slim\Exception\MethodNotAllowedException;
use Slim\Exception\NotFoundException;
use Slim\Views\TwigExtension;
use Slim\Http\Environment;
use Slim\Http\Uri;
use Slim\Views\Twig;

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
    ]);

    $router = $container->get('router');
    $uri = Uri::createFromEnvironment(new Environment($_SERVER));
    $view->addExtension(new TwigExtension($router, $uri));

    return $view;
};

$container['db'] = function () {
    return new Db();
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