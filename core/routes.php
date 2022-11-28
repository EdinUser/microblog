<?php

use MicroBlog\Controllers\PostController;
use Slim\App;

use MicroBlog\Controllers\UserController;

/**
 * @var App $app
 */
$app
  ->get('/', function ($request, $response) {
      return $this->view->render($response, 'index.html.twig');
  })
  ->setName('home');

$app->group('/user', function () {
    $this
      ->get('/login', UserController::class . ':doLogin')
      ->setName('user.login');

    $this
      ->post('/login', UserController::class . ':processLogin')
      ->setName('user.login_process');
});

$app->group('/post', function () {
    $this
      ->get('/list', PostController::class . ':showPosts')
      ->setName('post.show');

    $this
      ->get('{post_slug}', PostController::class . ':showSinglePost')
      ->setName('post.show');

    $this
      ->post('', PostController::class . ':updatePost')
      ->setName('post.update');
});
