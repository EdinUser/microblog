<?php

namespace MicroBlog\Controllers;

use MicroBlog\Models\Post;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;

class PostController extends BaseController
{
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
            $existingPostData = $this->container->db->sql_query_table(
              '*',
              'posts',
              array(
                'id' => $args['id'],
              ),
              'single'
            );
        }

        return $this->container->view->render($response, 'posts/manage_post.html.twig');
    }

    /**
     * Save/Update a post.
     *
     * @param Request  $request
     * @param Response $response
     * @param          $args
     *
     * @return Response
     */
    function savePost(Request $request, Response $response, $args): Response
    {
        // Very bad, but don't know how to do it!
        $dataForInsert = (new Post())->prepareInsert($request->getParams());

        $newPostId = $this->container->db->sql_upsert(
          'posts',
          array(),
          $dataForInsert,
          'INSERT'
        );

        if (!empty($newPostId)) {
            $existingPostData = $this->container->db->sql_query_table(
              'slug',
              'posts',
              array(
                'post_id' => $newPostId,
              ),
              'single'
            );

            return $response->withRedirect($this->container->router->pathFor('post.show', ['post_slug' => $existingPostData['slug'] . '.html']));
        } else {
            return $response->withRedirect($this->container->router->pathFor('post.show'), 503);
        }
    }

    /**
     * Show a single Post
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
        dump($existingPostData);
    }
}