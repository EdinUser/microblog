<?php

use MicroBlog\Controllers\InstallController;
use MicroBlog\Controllers\PictureController;
use MicroBlog\Controllers\UploadController;
use Slim\App;

use MicroBlog\Middleware\Auth;

use MicroBlog\Controllers\PostController;
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
    // List posts
    $this
      ->get('/list[/p{page}]', PostController::class . ':listPosts')
      ->setName('post.list');

    // Upload/Update post
    $this
      ->get('/send[/{id}]', PostController::class . ':managePost')
      ->add(new Auth($container->router))
      ->setName('post.send');

    // Show single post by slug
    $this
      ->get('[/{post_slug}.html]', PostController::class . ':showSinglePost')
      ->setName('post.show');

    // Process the POST data
    $this
      ->post('', PostController::class . ':savePost')
      ->add(new Auth($container->router))
      ->setName('post.update');
});

// Manage uploads, lock it for Admins only
$app
  ->post('/upload', UploadController::class . ':doUpload')
  ->add(new Auth($container->router))
  ->setName('picture.upload');

// Manage existing Pictures, lock it for Admins only
$app
  ->post('/picture/remove', PictureController::class . ':removePicture')
  ->add(new Auth($container->router))
  ->setName('picture.remove');

// No admin in DB, create new one
$app
  ->post('/install', InstallController::class . ':saveNewAdmin')
  ->setName('install.save');