<?php

namespace MicroBlog\Controllers;

use Exception;
use MicroBlog\Models\InstallModel;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;

class InstallController extends DependencyAware
{
    /**
     * @var InstallModel
     */
    private InstallModel $InstallModel;

    public function __construct(Container $container)
    {
        parent::__construct($container);
        $this->InstallModel = new InstallModel($container);
    }

    /**
     * Save entered Admin password
     *
     * @param Request  $request
     * @param Response $response
     *
     * @return Response
     * @throws Exception
     */
    function saveNewAdmin(Request $request, Response $response): Response
    {
        $createNewAdmin = $this->InstallModel->saveNewAdmin($request->getParams());
        if ($createNewAdmin !== 0) {
            return $response->withRedirect($this->container->router->pathFor('home'));
        }
    }
}