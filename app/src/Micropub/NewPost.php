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
        array $config,
        $files = []
    ) {
        $config['published'] = $this->validateDate($config);
        $this->properties = $config;
        $this->properties['uid'] = (new DateTimeImmutable())->format("YmdHis")."_".uniqid();
        unset($this->properties['access_token']);
        if (!isset($this->properties["h"])) {
            $this->properties["h"] = "entry";
        }

        foreach ($files as $file_key => $uploadedFile) {
            $this->properties['files'][$file_key] = $this->getFilePath().".".$uploadedFile['original_ext'];
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

    public function getFilePath()
    {
        return sprintf(
            "%s/%s",
            substr($this->getUid(), 0, 4),
            $this->getUid()
        );
    }

    private function validateDate($config)
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

    public function getUid()
    {
        return $this->properties['uid'];
    }
}