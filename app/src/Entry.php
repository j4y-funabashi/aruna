<?php

namespace Aruna;

use RuntimeException;

/**
 * Class Entry
 * @author yourname
 */
class Entry extends Post
{
    public function __construct($config)
    {
        $this->validateH($config);
        $config['published'] = $this->validateDate($config);
        $this->checkEntryHasData($config);
        $this->properties = $config;
    }

    public function getFilePath()
    {
        return sprintf(
            "%s/%s",
            $this->properties['published']->format("Y"),
            $this->properties['published']->format("YmdHis.u")
        );
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

    private function checkEntryHasData($config)
    {
        if (!isset($config['content']) && !isset($config['photo'])) {
            throw new RuntimeException('content or photo have to be set');
        }
    }
}
