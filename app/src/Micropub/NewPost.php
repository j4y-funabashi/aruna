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
        unset($config["access_token"]);
        $config['published'] = $this->validateDate($config);
        $this->properties = $config;
        $this->properties['uid'] = (new DateTimeImmutable())->format("YmdHis")."_".uniqid();
        if (!isset($this->properties["h"])) {
            $this->properties["h"] = "entry";
        }

        foreach ($files as $file_key => $uploadedFile) {
            $this->properties['files'][$file_key] = $this->getFilePath().".".$uploadedFile->getExtension();
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

    public function getUid()
    {
        return $this->properties['uid'];
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
}
