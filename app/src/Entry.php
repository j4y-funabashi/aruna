<?php

namespace Aruna;

use RuntimeException;

/**
 * Class Entry
 * @author yourname
 */
class Entry extends Post
{

    protected function validateH($config)
    {
        if (!isset($config['h'])) {
            throw new RuntimeException('"h" is not defined');
        }
        if ($config['h'] !== 'entry') {
            throw new RuntimeException($config['h'] . ' is not a valid "h"');
        }
    }

    protected function checkEntryHasData($config)
    {
        if (!isset($config['content']) && !isset($config['photo'])) {
            throw new RuntimeException('content or photo have to be set');
        }
    }
}
