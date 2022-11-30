<?php

namespace MicroBlog\Models;

use Exception;
use Imagick;
use ImagickException;
use MicroBlog\Controllers\DependencyAware;
use Psr\Http\Message\UploadedFileInterface;
use Slim\Container;
use Slim\Http\Request;

class Uploads extends DependencyAware
{
    /**
     * @var Imagick
     */
    private Imagick $Imagick;
    private int $width;
    private int $height;

    public function __construct(Container $container)
    {
        parent::__construct($container);
        $this->Imagick = new Imagick();

        /**
         * Hardcoded dimensions for the cropped file. Can be passed as params in future
         */
        $this->width = 500;
        $this->height = 250;
    }

    /**
     * @throws Exception
     */
    function imagickProcessFile($filesObject): bool
    {
        if (!is_dir($_ENV['ROOT_FOLDER'] . '/temp/upload_temp/')) {
            mkdir($_ENV['ROOT_FOLDER'] . '/temp/upload_temp/');
        }

        $uploadedFiles = $filesObject['pics'];
        foreach ($uploadedFiles as $currentFileIndex => $currentFile) {
            /**
             * @var $currentFile UploadedFileInterface
             */
            if ($currentFile->getError() !== UPLOAD_ERR_OK) {
                throw new Exception('Something is wrong with the uploaded image!', 300);
            }

            $movedFile = $_ENV['ROOT_FOLDER'] . '/temp/upload_temp/' . $currentFile->getClientFilename();
            $destinationFile = $_ENV['ROOT_FOLDER'] . '/temp/upload_temp/cr_' . $currentFile->getClientFilename();
            $currentFile->moveTo($movedFile);

            try {
                $this->Imagick->readImage($movedFile);
            }
            catch (Exception $e) {
                return $e->getMessage();
            }

            try {
                $checkConversion = $this->processImage($this->Imagick, $destinationFile);
                if ($checkConversion !== false) {
                    $_SESSION['uploads'][$currentFileIndex]['full'] = $_ENV['ROOT_FOLDER'] . '/temp/upload_temp/' . $currentFile->getClientFilename();
                    $_SESSION['uploads'][$currentFileIndex]['cropped'] = $destinationFile;
                }
            }
            catch (ImagickException $e) {
                dump($e->getMessage());
            }
        }

        if (!empty($_SESSION['uploads'])) {
            return true;
        }

        return false;

    }

    /**
     * @throws ImagickException
     */
    private function processImage(Imagick $imagick, string $destinationFile = ''): bool
    {
        $checkAlfa = $imagick->getImageAlphaChannel();

        if ($checkAlfa == 1) {
            $imagick->setImageFormat('png');
            $imagick->cropThumbnailImage($this->width, $this->height);
            $getFileExtension = pathinfo($destinationFile, PATHINFO_EXTENSION);
            if (strlen($getFileExtension) < 3) {
                $destinationFile = $destinationFile . ".png";
            }
        } else {
            $imagick->setImageFormat('jpg');
            $imagick->cropThumbnailImage($this->width, $this->height);
            $getFileExtension = pathinfo($destinationFile, PATHINFO_EXTENSION);
            if (strlen($getFileExtension) < 3) {
                $destinationFile = $destinationFile . ".jpg";
            }
        }

        return $imagick->writeImage($destinationFile);
    }
}