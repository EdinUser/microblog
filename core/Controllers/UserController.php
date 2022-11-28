<?php

namespace MicroBlog\Controllers;

use Exception;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;

use MicroBlog\Models\User;

class UserController extends DependencyAware
{
    /**
     * @var User
     */
    private User $UserModel;

    public function __construct(Container $container)
    {
        parent::__construct($container);
        $this->UserModel = new User($container);
    }

    /**
     * Show the Login template
     *
     * @param Request  $request
     * @param Response $response
     *
     * @return Response
     */
    public function doLogin(Request $request, Response $response): Response
    {
        if (!isset($_SESSION['siteid'])) {
            return $this->container->view->render($response, 'user/login.html.twig');
        } else {
            return $this->container->view->render($response, 'user/loggedin.html.twig');
        }
    }

    /**
     * Process login data provided by user
     *
     * @param Request  $request
     * @param Response $response
     *
     * @return Response
     * @throws Exception
     */
    public function processLogin(Request $request, Response $response): Response
    {
        $getUserData = $this->UserModel->processLogin($request->getParams());

        return $this->proceedWithLogin($getUserData, $response);
    }

    /**
     * Fill some info in $_SESSION
     *
     * @param array    $getUserData Data from DB
     * @param Response $response
     *
     * @return Response
     */
    private function proceedWithLogin(array $getUserData, Response $response): Response
    {
        $_SESSION['siteid'] = $getUserData['user_id'];
        if ((int)$getUserData['is_admin'] === 1) {
            $_SESSION['is_admin'] = true;
        }

        $_SESSION['user_name'] = $getUserData['user_name'];

        return $response->withRedirect($this->container->router->pathFor('home'));
    }

    /**
     * OK, time to go out now. Bye-bye!
     *
     * @param Request  $request
     * @param Response $response
     *
     * @return Response
     */
    function logOut(Request $request, Response $response): Response
    {
        session_destroy();

        return $response->withRedirect($this->container->router->pathFor('home'));
    }
}