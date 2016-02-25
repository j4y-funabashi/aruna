<?php

namespace Aruna;

/**
 * Class CreateEntryHandler
 * @author yourname
 */
class CreateEntryHandler
{
    public function __construct(
        PostRepository $postRepository
    ) {
        $this->postRepository = $postRepository;
    }

    public function handle(CreateEntryCommand $command)
    {
        $post = new Post($command->getEntry(), $command->getFiles());
        $this->postRepository->save($post, $command->getFiles());
        return $post;
    }
}
