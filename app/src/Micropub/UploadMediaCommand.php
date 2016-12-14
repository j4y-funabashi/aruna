<?php

namespace Aruna\Micropub;

class UploadMediaCommand
{
    private $file;
    private $token;

    public function __construct($config)
    {
        $this->file = $config["file"];
        $this->token = $config["token"];
    }

    public function getFile()
    {
        return $this->file;
    }

    public function getAccessToken()
    {
        return $this->token;
    }
}
