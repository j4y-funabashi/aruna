<?php

namespace Aruna;

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
        $payload = array(
            "items" => $this->postsRepository->findById($command->get("post_id"))
        );

        return new Found($payload);
    }
}
