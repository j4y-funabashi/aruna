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
        if ($config['h'] !== 'entry') {
            throw new RuntimeException($config['h'] . ' is not a valid "h"');
        }
        unset($config['h']);

        try {
            $config['published'] = (isset($config['published']))
                ? new DateTimeImmutable($config['published'])
                : new DateTimeImmutable();
            $config['published'] = $config['published']->format("Y-m-d H:i:s");
        } catch (\Exception $e) {
            throw new RuntimeException($config['published'] . ' is not a valid date');
        }

        if (!isset($config['content']) && !isset($config['photo'])) {
            throw new RuntimeException('content or photo have to be set');
        }

        $this->properties = $config;
    }

    public function jsonSerialize()
    {
        return $this->properties;
    }
}
