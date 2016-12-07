<?php

namespace Aruna\Micropub;

/**
 * Class CreatePostCommand
 * @author yourname
 */
class CreatePostCommand
{

    public function __construct(
        array $entry,
        array $files,
        $access_token
    ) {
        unset($entry["access_token"]);
        $this->entry = $entry;
        $this->files = $files;
        $this->access_token = $access_token;
    }

    public function getEntry()
    {
        return $this->entry;
    }

    public function getFiles()
    {
        return $this->files;
    }

    public function getAccessToken()
    {
        return $this->access_token;
    }
}
