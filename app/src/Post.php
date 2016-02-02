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

    public function jsonSerialize()
    {
        $out = $this->properties;
        $out['published'] = $this->properties['published']->format("c");
        return $out;
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


    public function asJson()
    {
        return json_encode($this);
    }
}
