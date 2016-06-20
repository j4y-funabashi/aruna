<?php

namespace Aruna\Action;

use Intervention\Image\ImageManagerStatic as Image;

/**
 * Class ImageResizer
 * @author yourname
 */
class ImageResizer
{

    public function __construct(
        $log,
        $root_dir,
        $thumbnails_dir
    ) {
        $this->log = $log;
        $this->root_dir = $root_dir;
        $this->thumbnails_dir = $thumbnails_dir;
        Image::configure(array('driver' => 'imagick'));
    }

    public function resize($photo_path)
    {
        $in_path = $this->root_dir."/".$photo_path;
        $out_path = $this->thumbnails_dir."/".$photo_path;
        if (file_exists($out_path)) {
            $m = sprintf(
                "Photo already exists [%s]",
                $out_path
            );
            return;
        }

        $this->ensureDirectory(dirname($out_path));

        $img = Image::make($in_path);
        $img->fit(640);
        $img->save($out_path);
    }

    protected function ensureDirectory($root)
    {
        if (!is_dir($root)) {
            $umask = umask(0);
            if (!mkdir($root, 0755, true)) {
                throw new \Exception();
            }
            umask($umask);
        }
        return realpath($root);
    }
}
