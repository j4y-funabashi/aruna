<?php

namespace Aruna;

use DateTimeImmutable;
use RuntimeException;

/**
 * Class Entry
 * @author yourname
 */
class Entry implements \JsonSerializable
{
    private $properties;

    public function __construct($config)
    {
        $this->validateH($config);
        $config['published'] = $this->validateDate($config);
        $this->checkEntryHasData($config);

        unset($config['h']);
        $this->properties = $config;
    }

    public function jsonSerialize()
    {
        $out = $this->properties;
        $out['published'] = $this->properties['published']->format("c");
        return $out;
    }

    public function getFilePath()
    {
        return sprintf(
            "%s/%s",
            $this->properties['published']->format("Y"),
            $this->properties['published']->format("YmdHis.u")
        );
    }

    public function asJson()
    {
        return json_encode($this);
    }

    private function validateH($config)
    {
        if (!isset($config['h'])) {
            throw new RuntimeException('"h" is not defined');
        }
        if ($config['h'] !== 'entry') {
            throw new RuntimeException($config['h'] . ' is not a valid "h"');
        }
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

    private function checkEntryHasData($config)
    {
        if (!isset($config['content']) && !isset($config['photo'])) {
            throw new RuntimeException('content or photo have to be set');
        }
    }
}
