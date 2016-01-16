<?php

namespace Aruna;

/**
 * Class CreateEntryCommand
 * @author yourname
 */
class CreateEntryCommand
{

    public function __construct(
        array $entry
    ) {
        $this->entry = $entry;
    }

    public function getEntry()
    {
        return $this->entry;
    }
}
