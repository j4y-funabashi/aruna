<?php

namespace Aruna;

use DateTimeImmutable;

/**
 * Class Event
 * @author yourname
 */
class Event
{

    public function __construct(array $properties)
    {
        $this->properties = $properties;
        $this->properties['published'] = $this->createDate($this->properties);
        $this->properties['uid'] = $this->createUid($this->properties);
    }

    protected function createDate($config)
    {
        try {
            return (isset($config['published']))
                ? new DateTimeImmutable($config['published'])
                : new DateTimeImmutable();
        } catch (\Exception $e) {
            throw new RuntimeException($config['published'] . ' is not a valid date');
        }
    }

    protected function createUid($config)
    {
        return (isset($config['uid']))
            ? new EventUid($config['uid'])
            : new EventUid();
    }
}
