<?php

namespace Aruna;

/**
 * Class CreatePostCommand
 * @author yourname
 */
class CreatePostCommand
{

    public function __construct(
        array $entry,
        array $files
    ) {
        $this->entry = $entry;
        $this->files = $files;
    }

    public function getEntry()
    {
        return $this->entry;
    }

    public function getFiles()
    {
        return $this->files;
    }
}
