<?php

namespace Aruna;

/**
 * Class CreateEntryCommand
 * @author yourname
 */
class CreateEntryCommand
{

    public function __construct(
        array $entry,
        array $files
    ) {
        $this->entry = $entry;
        $this->files = $files;
    }

    public function getEntry()
    {
        return $this->entry;
    }

    public function getFiles()
    {
        return $this->files;
    }
}
