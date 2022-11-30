<?php

namespace MicroBlog\Controllers;

use Exception;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;

use MicroBlog\Models\Uploads;

class UploadController extends DependencyAware
{
    /**
     * @var Uploads
     */
    private Uploads $UploadModel;

    public function __construct(Container $container)
    {
        parent::__construct($container);
        $this->UploadModel = new Uploads($container);
    }

    /**
     * Do the picture upload - crop and save into a $_SESSION to be proceeded further
     *
     * @param Request  $request
     * @param Response $response
     *
     * @return Response
     * @throws Exception
     */
    function doUpload(Request $request, Response $response): Response
    {
        try {
            $this->UploadModel->imagickProcessFile($request->getUploadedFiles());
            $responseUpload['status'] = true;
        }
        catch (Exception $e) {
            $responseUpload['status'] = false;
            $responseUpload['error'] = $e->getMessage();
            $responseUpload['error_code'] = $e->getCode();
        }

        return $response->withJson($responseUpload);
    }
}