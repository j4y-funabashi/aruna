<?php

namespace Aruna;

/**
 * Class CreatePostHandler
 * @author yourname
 */
class CreatePostHandler
{
    public function __construct(
        PostRepositoryWriter $postRepository
    ) {
        $this->postRepository = $postRepository;
    }

    public function handle(CreatePostCommand $command)
    {
        $post = new Post($command->getEntry(), $command->getFiles());
        $this->postRepository->save($post, $command->getFiles());
        return $post;
    }
}
