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

        if (isset($data['pics'])) {
            unset($data['pics']);
        }

        $data['content'] = trim($data['content']);
        $data['title'] = trim($data['title']);

        return (array)$data;
    }

    /**
     * @param array  $limiters
     * @param string $returnType
     *
     * @return mixed
     */
    public function read(array $limiters, string $returnType): mixed
    {
        $return = $this->container->db->sql_query_table(
          '*',
          'posts',
          $limiters,
          $returnType
        );

        return $this->readPictures($return);
    }

    /**
     * Check for existing pictures and return if any
     *
     * @param $return
     *
     * @return array
     */
    private function readPictures($return): array
    {
        if (isset($return['post_id'])) {
            $readSinglePostPictures = $this->globPostPictures($return['post_id']);
            if (!empty($readSinglePostPictures)) {
                $return['pics'] = $readSinglePostPictures;
            }
        } else {
            foreach ($return as $postIndex => &$post) {
                $readSinglePostPictures = $this->globPostPictures($post->post_id);
                if (!empty($readSinglePostPictures)) {
                    $post->pics = $readSinglePostPictures;
                }
            }
        }

        return $return;
    }

    /**
     * Just a simple method to fetch all cropped pictures
     *
     * @param $postId
     *
     * @return array
     */
    private function globPostPictures($postId): array
    {
        $getAllPictures = glob($_ENV['ROOT_FOLDER'] . '/public_html/i/' . $postId . '/*');
        if (empty($getAllPictures)) {
            return array();
        }

        $returnPics = array();
        foreach ($getAllPictures as $currentFile) {
            $baseFileName = basename($currentFile);
            if (stripos($currentFile, 'cr_') !== false) {
                $returnPics[substr($baseFileName, 3)]['cropped'] = '/i/' . $postId . '/' . $baseFileName;
            } else {
                $returnPics[$baseFileName]['full'] = '/i/' . $postId . '/' . $baseFileName;
                $returnPics[$baseFileName]['base_name'] = $baseFileName;
            }
        }

        return $returnPics;
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

        // Process uploads (if any) and remove them from $_SESSION
        if (isset($_SESSION['uploads'])) {
            $this->processUploadedPics($_SESSION['uploads'], (int)$newPostId === 0 ? $existingPostId : (int)$newPostId);
            unset($_SESSION['uploads']);
        }

        return $this->read(['post_id' => (int)$newPostId === 0 ? $existingPostId : (int)$newPostId], 'single');
    }

    private function processUploadedPics(mixed $uploads, $postId = 0)
    {
        foreach ($uploads as $uploadedFile) {
            foreach ($uploadedFile as $file) {
                if (file_exists($file)) {
                    $properDestination = $_ENV['ROOT_FOLDER'] . '/public_html/i/' . $postId;
                    if (!is_dir($properDestination)) {
                        $testDirCreation = mkdir($properDestination);
                    }

                    $checkCroppedFileCopy = copy($file, $properDestination . "/" . basename($file));
                    if ($checkCroppedFileCopy === true) {
                        unlink($file);
                    }
                }
            }
        }
    }

}