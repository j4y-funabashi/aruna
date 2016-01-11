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
        return $this->properties;
    }

    private function validateH($config)
    {
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
            return $published->format("Y-m-d H:i:s");
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
