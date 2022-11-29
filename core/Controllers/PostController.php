<?php

namespace MicroBlog\Controllers;

use MicroBlog\Models\Post;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;

class PostController extends DependencyAware
{
    /**
     * @var Post
     */
    private Post $PostModel;

    public function __construct(Container $container)
    {
        parent::__construct($container);
        $this->PostModel = new Post($container);
    }

    /**
     * Send/Update a Blog Post
     *
     * @param Request  $request
     * @param Response $response
     * @param          $args
     *
     * @return mixed
     */
    function managePost(Request $request, Response $response, $args): mixed
    {
        if (!empty($args['id'])) {
            $existingPost = $this->PostModel->read((int)$args['id']);
        }

        return $this->container->view->render($response, 'posts/manage_post.html.twig', $existingPost ?? array());
    }

    /**
     * Save/Update a post.
     *
     * @param Request  $request
     * @param Response $response
     *
     * @return Response
     */
    function savePost(Request $request, Response $response): Response
    {
        $existingPostData = $this->PostModel->save($request->getParams());

        if (!empty($existingPostData)) {
            return $response->withRedirect($this->container->router->pathFor('post.show', ['post_slug' => $existingPostData['slug']]));
        } else {
            return $response->withRedirect($this->container->router->pathFor('post.show'), 503);
        }
    }

    /**
     * Show a single Post
     *
     * @param Request  $request
     * @param Response $response
     * @param          $args
     *
     * @return void
     */
    function showSinglePost(Request $request, Response $response, $args)
    {
        $existingPostData = $this->container->db->sql_query_table(
          '*',
          'posts',
          array(
            'slug' => $args['post_slug'],
          ),
          'single'
        );
        return $this->container->view->render($response, 'posts/post.html.twig', $existingPostData ?? array());
    }


    function listPosts(Request $request, Response $response, $args){

    }
}