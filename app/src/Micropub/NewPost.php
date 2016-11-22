<?php

namespace Aruna\Micropub;

use DateTimeImmutable;
use RuntimeException;

/**
 * Class NewPost
 * @author yourname
 */
class NewPost implements \JsonSerializable
{
    protected $properties;

    public function __construct(
        array $config
    ) {
        $config = $this->removeAccessToken($config);
        $config = $this->addHIfNotExists($config);
        $config = $this->addUid($config);
        $config = $this->validateDate($config);
        $this->properties = $config;
    }

    public function jsonSerialize()
    {
        $eventData = $this->properties;
        $eventData['published'] = $this->properties['published']->format("c");
        foreach ($eventData as $k => $v) {
            if (!is_array($v)) {
                $eventData[$k] = array($v);
            }
        }
        $out = [
            "eventType" => $this->getEventType(),
            "eventVersion" => $this->properties['published']->format("U"),
            "eventID" => $this->getUid(),
            "eventData" => [
                "type" => ["h-".$this->properties["h"]],
                "properties" => $eventData
            ]
        ];
        return $out;
    }

    public function asJson()
    {
        return json_encode($this);
    }

    public function getFilePath()
    {
        return sprintf(
            "%s/%s",
            $this->properties['published']->format("Y"),
            $this->getUid()
        );
    }

    public function getUid()
    {
        return $this->properties['uid'];
    }

    private function addUid($config)
    {
        if (!isset($config["uid"])) {
            $config['uid'] = (new DateTimeImmutable())->format("YmdHis")."_".uniqid();
        }
        return $config;
    }

    private function validateDate($config)
    {
        try {
            $config["published"] = (isset($config['published']))
                ? new DateTimeImmutable($config['published'])
                : new DateTimeImmutable();
            return $config;
        } catch (\Exception $e) {
            throw new RuntimeException($config['published'] . ' is not a valid date');
        }
    }

    private function addHIfNotExists($config)
    {
        if (!isset($config["h"])) {
            $config["h"] = "entry";
        }
        return $config;
    }

    private function removeAccessToken($config)
    {
        unset($config["access_token"]);
        return $config;
    }

    private function getEventType()
    {
        return "PostCreated";
    }
}
