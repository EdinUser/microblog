<?php

namespace MicroBlog\Controllers;

use Exception;
use Slim\Http\Request;
use Slim\Http\Response;

class UserController extends BaseController
{
    /**
     * Show the Login template
     *
     * @param Request  $request
     * @param Response $response
     *
     * @return mixed
     */
    public function doLogin(Request $request, Response $response)
    {
        return $this->container->view->render($response, 'user/login.html.twig');
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
        $getUserData = $this->container->db->sql_query_table(
          '*',
          'users',
          array(
            'is_admin'  => 1,
            'is_active' => 1,
            'user_name' => $request->getParam('username'),
          ),
          'single'
        );

        if (empty($getUserData)) {
            throw new Exception('No user found', 100);
        }

        $checkLogin = password_verify($request->getParam('password'), $getUserData['password']);
        if (!$checkLogin) {
            throw new Exception('Login problem', 101);
        }

        if ((int)$getUserData['is_active'] !== 1) {
            throw new Exception('User not active!', 102);
        }

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
        if ($getUserData['is_admin'] === 1) {
            $_SESSION['is_admin'] = true;
        }

        $_SESSION['user_name'] = $getUserData['user_name'];

        return $response->withRedirect($this->container->router->pathFor('home'));
    }
}