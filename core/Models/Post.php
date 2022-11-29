<?php

namespace MicroBlog\Models;

use MicroBlog\Controllers\DependencyAware;
use MicroBlog\Interfaces\ModelInterface;

class Post extends DependencyAware implements ModelInterface
{
    /**
     * Some clean up of data for Post
     *
     * @param array $data
     *
     * @return array
     */
    function prepareInsert(array $data): array
    {
        $data = array_filter($data);
        if (empty($data['post_id'])) {
            $data['author_id'] = $_SESSION['siteid'];
        } else {
            $data['date_edited'] = date("Y-m-d H:i:s");
            $data['edited_by'] = $_SESSION['siteid'];
        }

        $data['content'] = trim($data['content']);
        $data['title'] = trim($data['title']);

        return (array)$data;
    }

    /**
     * @param int $id
     *
     * @return mixed
     */
    public function read(int $id): mixed
    {
        return $this->container->db->sql_query_table(
          '*',
          'posts',
          array(
            'post_id' => $id,
          ),
          'single'
        );
    }

    /**
     * @param $data
     *
     * @return array
     */
    public function save($data): array
    {
        $dataForInsert = $this->prepareInsert($data);

        $do = 'INSERT';
        $existingPostId = 0;
        if ($dataForInsert['post_id']) {
            $do = 'UPDATE';
            $whereArray = array(
              'post_id' => (int)$dataForInsert['post_id'],
            );
            $existingPostId = (int)$dataForInsert['post_id'];
        }

        $newPostId = $this->container->db->sql_upsert(
          'posts',
          $whereArray ?? array(),
          $dataForInsert,
          $do
        );

        return $this->read((int)$newPostId === 0 ? $existingPostId : (int)$newPostId);
    }

}