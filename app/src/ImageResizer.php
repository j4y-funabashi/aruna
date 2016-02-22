<?php

namespace Aruna;

use Intervention\Image\ImageManagerStatic as Image;

/**
 * Class ImageResizer
 * @author yourname
 */
class ImageResizer
{

    public function __construct()
    {
        Image::configure(array('driver' => 'imagick'));
    }

    public function resize($entry, $base_path)
    {
        if (!$entry->hasPhoto()) {
            return false;
        }
        $img = Image::make("/tmp/aruna/".$entry->getPhotoPath());
        $img->fit(1080);
        $out_path = "/tmp/aruna/".$base_path."_square.jpg";
        $img->save($out_path);
    }
}
