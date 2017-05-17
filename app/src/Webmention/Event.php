<?php

namespace Aruna\Webmention;

use DateTimeImmutable;

/**
 * Class Event
 * @author yourname
 */
class Event implements \JsonSerializable
{

    public function __construct(array $properties)
    {
        $this->properties = $properties;
        $this->properties['published'] = $this->createDate($this->properties);
        $this->properties['uid'] = $this->properties['published']->format("YmdHis")."_".uniqid();
    }

    public function getUid()
    {
        return $this->properties['uid'];
    }

    public function getFilePath()
    {
        return sprintf(
            "%s/%s",
            $this->properties["published"]->format("Y"),
            $this->getUid()
        );
    }

    public function jsonSerialize()
    {
        $eventData = $this->properties;
        $eventData['published'] = $this->properties['published']->format("c");
        $out = [
            "eventType" => "WebmentionReceived",
            "eventVersion" => $this->getUid(),
            "eventID" => $this->getUid(),
            "eventData" => $eventData
        ];
        return $out;
    }

    protected function createDate($config)
    {
        try {
            return (isset($config['published']))
                ? new DateTimeImmutable($config['published'])
                : new DateTimeImmutable();
        } catch (\Exception $e) {
            throw new \RuntimeException($config['published'] . ' is not a valid date');
        }
    }
}
