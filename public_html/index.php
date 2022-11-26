<?php

use Slim\App;
use Slim\Exception\MethodNotAllowedException;
use Slim\Exception\NotFoundException;
use Slim\Http\Environment;
use Slim\Http\Uri;
use Slim\Views\Twig;
use Slim\Views\TwigExtension;

require '../vendor/autoload.php';

// Use .env file
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . "/../");
$dotenv->load();

// Init Slim
$app = new App(
  [
    'settings' =>
      [
        'displayErrorDetails' => true,
      ],
  ]
);

// Init container
$container = $app->getContainer();

// Twig
// Register component on container
$container['view'] = function ($container) {
    $view = new Twig($_ENV['ROOT_FOLDER'] . '/templates', [
      'cache' => false,
    ]);

    // Instantiate and add Slim specific extension
    $router = $container->get('router');
    $uri = Uri::createFromEnvironment(new Environment($_SERVER));
    $view->addExtension(new TwigExtension($router, $uri));

    return $view;
};


// Routes
$app->get('/', function ($request, $response) {
    return $this->view->render($response, 'base.html.twig');
});

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