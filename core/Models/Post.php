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

        $newPostId = $this->container->db->sql_upsert(
          'posts',
          array(),
          $dataForInsert,
          'INSERT'
        );

        return $this->read((int)$newPostId);
    }

}