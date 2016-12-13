<?php

namespace Aruna\Micropub;

class ExtractJpgMetadata
{

    public function __invoke(UploadedFile $file)
    {
        $out = [];
        $exif = exif_read_data($file->getRealPath());
        if (isset($exif["DateTimeOriginal"])) {
            $out["published"] = \DateTimeImmutable::createFromFormat(
                "Y:m:d H:i:s",
                $exif["DateTimeOriginal"]
            )->format("c");
        }
        return $out;
    }
}
