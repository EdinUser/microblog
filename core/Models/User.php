<?php

namespace MicroBlog\Models;

use Exception;
use MicroBlog\Controllers\DependencyAware;

class User extends DependencyAware
{

    /**
     * @param $post
     *
     * @return array
     * @throws Exception
     */
    function processLogin($post): array
    {
        $getUserData = $this->container->db->sql_query_table(
          '*',
          'users',
          array(
            'is_admin'  => 1,
            'is_active' => 1,
            'user_name' => $post['username'],
          ),
          'single'
        );

        if (empty($getUserData)) {
            throw new Exception('No user found', 100);
        }

        $checkLogin = password_verify($post['password'], $getUserData['password']);
        if (!$checkLogin) {
            throw new Exception('Login problem', 101);
        }

        if ((int)$getUserData['is_active'] !== 1) {
            throw new Exception('User not active!', 102);
        }

        return $getUserData;
    }
}