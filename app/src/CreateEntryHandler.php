<?php

namespace Aruna;

/**
 * Class CreateEntryHandler
 * @author yourname
 */
class CreateEntryHandler
{
    public function __construct(EntryRepository $entryRepository)
    {
        $this->entryRepository = $entryRepository;
    }

    public function handle(CreateEntryCommand $command)
    {
        $files = $command->getFiles();
        $entry = new Post($command->getEntry(), $files);
        $this->entryRepository->save($entry, $files);
        return $entry;
    }
}
