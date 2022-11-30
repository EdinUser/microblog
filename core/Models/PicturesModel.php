<?php

namespace MicroBlog\Models;

use MicroBlog\Controllers\DependencyAware;

class PicturesModel extends DependencyAware
{
    function removePictureFromServer($postId = 0, $pictureName = '')
    {
        $getPicture = glob($_ENV['ROOT_FOLDER'] . '/public_html/i/' . $postId . '/' . $pictureName);
        if (!empty($getPicture)) {
            $getCroppedPicture = glob($_ENV['ROOT_FOLDER'] . '/public_html/i/' . $postId . '/cr_' . $pictureName);
            $unlink = unlink($getPicture[0]);
            if ($unlink) {
                $unlink = unlink($getCroppedPicture[0]);
            }
        }

        return array('status' => $unlink);
    }

}