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

    public function resize($in_path, $out_path)
    {
        $in_path = $this->root_dir."/".$in_path;
        $out_path = $this->thumbnails_dir."/".$out_path;
        if (file_exists($out_path)) {
            return;
        }
        if ($in_path == $out_path) {
            $m = sprintf(
                "Source photo cannot be target photo [%s] [%s]",
                $in_path,
                $out_path
            );
            $this->log->critical($m);
            return;
        }

        $this->ensureDirectory(dirname($out_path));

        $img = Image::make($in_path);
        $img->fit(640);

        $m = sprintf(
            "Resizing [%s] to [%s]",
            $in_path,
            $out_path
        );
        $this->log->info($m);

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
