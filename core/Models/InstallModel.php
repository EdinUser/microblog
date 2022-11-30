<?php

namespace MicroBlog\Models;

use Exception;
use MicroBlog\Controllers\DependencyAware;

class InstallModel extends DependencyAware
{
    /**
     * @param array $postedData
     *
     * @return int
     * @throws Exception
     */
    function saveNewAdmin(array $postedData): int
    {
        if (!$postedData['admin_pass'] || !$postedData['admin_pass_repeat'] || $postedData['admin_pass_repeat'] !== $postedData['admin_pass']) {
            throw new Exception('Passwords do not match!', 400);
        }

        // has the password
        $buildNewPass = password_hash($postedData['admin_pass'], PASSWORD_BCRYPT);

        return (int)$this->container->db->sql_upsert(
          'users',
          array(),
          array(
            'user_name' => $postedData['admin_username'],
            'password'  => $buildNewPass,
            'is_admin'  => 1,
            'is_active' => 1,
          ),
          'INSERT'
        );
    }
}