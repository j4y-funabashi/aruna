<?php

namespace Aruna;

/**
 * Class CreateEntryHandler
 * @author yourname
 */
class CreateEntryHandler
{
    public function __construct(
        EntryRepository $entryRepository,
        ImageResizer $imageResizer
    ) {
        $this->entryRepository = $entryRepository;
        $this->imageResizer = $imageResizer;
    }

    public function handle(CreateEntryCommand $command)
    {
        $files = $command->getFiles();
        $entry = $command->getEntry();
        $entry = new Post($entry, $files);
        $this->entryRepository->save($entry, $files);
        $this->imageResizer->resize($entry, $entry->getFilePath());
        return $entry;
    }
}
