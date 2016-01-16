<?php

namespace Aruna;

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
        $this->filesystem->write(
            $entry->getFilePath(),
            $entry->asJson()
        );
    }
}
