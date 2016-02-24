<?php

namespace Aruna;

/**
 * Class CreateEntryHandler
 * @author yourname
 */
class CreateEntryHandler
{
    public function __construct(
        PostRepository $entryRepository
    ) {
        $this->entryRepository = $entryRepository;
    }

    public function handle(CreateEntryCommand $command)
    {
        $post = new Post($command->getEntry(), $command->getFiles());
        $this->entryRepository->save($post, $command->getFiles());
        return $post;
    }
}
