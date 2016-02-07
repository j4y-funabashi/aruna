<?php

namespace Aruna;

use League\Flysystem\FileExistsException;
use RuntimeException;

/**
 * Class EntryRepository
 * @author yourname
 */
class EntryRepository
{
    public function __construct($filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function save(Entry $entry)
    {
        try {
            $this->filesystem->write(
                $entry->getFilePath(),
                $entry->asJson()
            );
        } catch (FileExistsException $e) {
            throw new RuntimeException($e->getMessage());
        }
    }
}
