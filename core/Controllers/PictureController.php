<?php

namespace MicroBlog\Controllers;

use MicroBlog\Models\PicturesModel;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;

class PictureController extends DependencyAware
{
    /**
     * @var PicturesModel
     */
    private PicturesModel $PicturesModel;

    public function __construct(Container $container)
    {
        parent::__construct($container);
        $this->PicturesModel = new PicturesModel($container);
    }

    /**
     * Remove picture from Post
     *
     * @param Request  $request
     * @param Response $response
     *
     * @return Response
     */
    function removePicture(Request $request, Response $response): Response
    {
        $postId = $request->getParam('post_id');
        $pictureName = $request->getParam('picture');

//        $this->PicturesModel->removePictureFromServer($postId, $pictureName);
        return $response->withJson($this->PicturesModel->removePictureFromServer($postId, $pictureName));
    }
}