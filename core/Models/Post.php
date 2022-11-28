<?php

namespace MicroBlog\Models;

use Slim\Http\Request;

class Post
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

    function postPageTitle()
    {
        return "{$this->title}";
    }

}