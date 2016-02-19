<?php

namespace Aruna;

use DateTimeImmutable;
use RuntimeException;

/**
 * Class Post
 * @author yourname
 */
class Post implements \JsonSerializable
{
    protected $properties;

    public function __construct($config, $files = [])
    {
        $config['published'] = $this->validateDate($config);
        $this->properties = $config;
        $this->uid = uniqid();

        foreach ($files as $file_key => $uploadedFile) {
            $this->properties['files'][$file_key] = $this->getFilePath()."_".$this->getUid().".".$uploadedFile->getClientOriginalExtension();
        }
    }

    public function jsonSerialize()
    {
        $out = $this->properties;
        $out['published'] = $this->properties['published']->format("c");
        return $out;
    }

    public function asJson()
    {
        return json_encode($this);
    }

    protected function validateDate($config)
    {
        try {
            $published = (isset($config['published']))
                ? new DateTimeImmutable($config['published'])
                : new DateTimeImmutable();
            return $published;
        } catch (\Exception $e) {
            throw new RuntimeException($config['published'] . ' is not a valid date');
        }
    }

    public function getFilePath()
    {
        return sprintf(
            "%s/%s",
            $this->getYear(),
            $this->properties['published']->format("YmdHis")
        );
    }

    public function getYear()
    {
        return $this->properties['published']->format("Y");
    }

    public function getUid()
    {
        return $this->uid;
    }

    public function getPostId()
    {
        return $this->properties['published']->format("YmdHis")."_".$this->getUid();
    }
}
