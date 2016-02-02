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

    public function __construct($config)
    {
        $this->validateH($config);
        $config['published'] = $this->validateDate($config);
        $this->checkEntryHasData($config);
        $this->properties = $config;
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
            "%s/%s.%s.json",
            $this->properties['published']->format("Y"),
            $this->properties['published']->format("YmdHis"),
            substr(sha1($this->asJson()), 0, 8)
        );
    }
}
