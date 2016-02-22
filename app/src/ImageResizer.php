<?php

namespace Aruna;

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

    public function resize($entry, $base_path)
    {
        if (!$entry->hasPhoto()) {
            return false;
        }
        $img = Image::make($this->root_dir."/".$entry->getPhotoPath());
        $img->fit(1080);
        $out_path = $this->root_dir."/".$entry->getFilePath()."_square.jpg";
        $img->save($out_path);
    }
}
