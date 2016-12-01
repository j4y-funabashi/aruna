<?php

namespace Aruna\Reader;

use Aruna\Response\Gone;
use Aruna\Response\Found;
use Aruna\Handler;

class ShowPostHandler implements Handler
{
    public function __construct(
        $postRepository,
        $url_generator
    ) {
        $this->postsRepository = $postRepository;
        $this->url_generator = $url_generator;
    }

    public function handle($command)
    {
        $post = $this->postsRepository->findById(
            $command->get("post_id")
        );
        $payload = array(
            "items" => $post
        );

        if ($post[0]->isDeleted()) {
            return new Gone($payload);
        }
        return new Found($payload);
    }
}
