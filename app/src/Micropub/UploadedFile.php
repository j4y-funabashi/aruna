<?php

namespace Aruna\Micropub;

class UploadedFile
{
    public function __construct(
        $real_path,
        $original_ext,
        $is_readable,
        $is_valid
    ) {
        $this->real_path = $real_path;
        $this->original_ext = $original_ext;
        $this->is_readable = $is_readable;
        $this->is_valid = $is_valid;
    }

    public function isValid()
    {
        return ($this->is_valid && $this->is_readable);
    }

    public function getExtension()
    {
        return $this->original_ext;
    }

    public function getRealPath()
    {
        return $this->real_path;
    }
}
