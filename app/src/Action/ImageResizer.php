<?php

namespace Aruna\Action;

use Intervention\Image\ImageManagerStatic as Image;

/**
 * Class ImageResizer
 * @author yourname
 */
class ImageResizer
{

    public function __construct($root_dir)
    {
        $this->root_dir = $root_dir;
        Image::configure(array('driver' => 'imagick'));
    }

    public function resize($photo_path)
    {
        $out_path = $this->root_dir."/".$photo_path;
        $img = Image::make($this->root_dir."/".$photo_path);
        $img->fit(1080);
        $img->save($out_path);
    }
}
