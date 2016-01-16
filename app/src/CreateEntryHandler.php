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
        $entry = new Entry($command->getEntry());
        $this->entryRepository->save($entry);
        return $entry;
    }
}
