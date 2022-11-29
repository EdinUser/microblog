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
     * @return Response
     */
    function managePost(Request $request, Response $response, $args): Response
    {
        if (!empty($args['id'])) {
            $existingPost = $this->PostModel->read(['post_id' => $args['id']], 'single');
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
     * @return Response
     */
    function showSinglePost(Request $request, Response $response, $args): Response
    {
        $existingPostData = $this->PostModel->read(['slug' => $args['post_slug']], 'single');

        return $this->container->view->render($response, 'posts/post.html.twig', $existingPostData ?? array());
    }

    /**
     * List all available and active posts
     *
     * @param Request  $request
     * @param Response $response
     * @param          $args
     *
     * @return Response
     */
    function listPosts(Request $request, Response $response, $args): Response
    {
        $existingPostData['posts'] = $this->PostModel->read(['is_active' => 1], 'multiple');
        $buildPagination = $this->container->pagination->doPagination($existingPostData['posts'], 'post.list');

        return $this->container->view->render(
          $response,
          'posts/list_posts.html.twig',
          array(
            'posts'      => $buildPagination['resultArray'],
            'pagination' => $buildPagination['resultTwig'],
          ) ?? array()
        );

    }
}