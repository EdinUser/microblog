<?php

namespace MicroBlog\Middleware;

use MicroBlog\Controllers\DependencyAware;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;

class Install extends DependencyAware
{
    public function __construct(Container $container)
    {
        parent::__construct($container);
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, $next)
    {
        if ($this->container->request->getMethod() !== 'POST' && $this->container->request->getUri()->getPath() !== '/install') {
            $checkAdmin = $this->container->db->sql_query_table(
              '*',
              'users',
              array(
                'is_admin'  => 1,
                'is_active' => 1,
              ),
              'single'
            );

            if ($checkAdmin === false) {
                return $this->container->view->render($response, 'user/install.html.twig');
            }
        }

        return $next($request, $response);
    }
}