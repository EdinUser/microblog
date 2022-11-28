<?php

namespace MicroBlog\Middleware;

use Slim\Http\Request;
use Slim\Http\Response;

class Auth
{
    /**
     * @var object
     */
    private object $router;

    public function __construct($router)
    {
        $this->router = $router;
    }

    /**
     * Check is user is admin
     *
     * @param Request  $request
     * @param Response $response
     * @param          $next
     *
     * @return mixed
     */
    public function __invoke(Request $request, Response $response, $next): mixed
    {
        if (!isset($_SESSION['is_admin'])) {
            $response = $response->withRedirect($this->router->pathFor('user.login'), 401);
        }

        return $next($request, $response);
    }
}