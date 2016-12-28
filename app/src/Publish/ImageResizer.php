<?php

namespace Aruna\Publish;

use Intervention\Image\ImageManagerStatic as Image;

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

    public function resize($in_path, $out_path, $width)
    {
        $in_path = $this->root_dir."/".$in_path;
        $out_path = $this->root_dir."/".$out_path;
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
        $img->orientate();
        $img->fit($width, null, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });
        $img->save($out_path);

        $m = sprintf("Resized [%s] to [%s]", $in_path, $out_path);
        $this->log->info($m);
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
