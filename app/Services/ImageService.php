<?php

namespace App\Services;

class ImageService
{
    public function compressImage($source, $destination, $quality) {
        $info = getimagesize($source);
        if ($info['mime'] == 'image/jpeg' || $info['mime'] == 'image/jpg') {
            $image = imagecreatefromjpeg($source);
            $ext = 'jpg';
        }
        elseif ($info['mime'] == 'image/gif') {
            $image = imagecreatefromgif($source);
            $ext = 'gif';
        }
        elseif ($info['mime'] == 'image/png') {
            $image = imagecreatefrompng($source);
            $ext = 'png';
        }
        elseif ($info['mime'] == 'image/webp') {
            $image = imagecreatefromwebp($source);
            $ext = 'webp';
        }
        $destination .= ".". $ext;
        imagejpeg($image, $destination, $quality);

        $path = explode('/', $destination);
        return $path[count($path) - 1];
    }
}
