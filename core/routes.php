<?php

use Slim\App;

use MicroBlog\Controllers\PostController;
use MicroBlog\Middleware\Auth;
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
      ->get('/logout', UserController::class . ':logOut')
      ->setName('user.logout');

    $this
      ->post('/login', UserController::class . ':processLogin')
      ->setName('user.login_process');
});

$app->group('/post', function () use ($container) {
    $this
      ->get('/list[/p{page}]', PostController::class . ':listPosts')
      ->setName('post.list');

    $this
      ->get('/send[/{id}]', PostController::class . ':managePost')
      ->add(new Auth($container->router))
      ->setName('post.send');

    $this
      ->get('[/{post_slug}.html]', PostController::class . ':showSinglePost')
      ->setName('post.show');

    $this
      ->post('', PostController::class . ':savePost')
      ->add(new Auth($container->router))
      ->setName('post.update');
});
