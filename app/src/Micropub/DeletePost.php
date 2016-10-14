<?php

namespace Aruna\Micropub;

class DeletePost
{
    public function __construct(
        $postsRepository
    ) {
        $this->postsRepository = $postsRepository;
    }

    public function __invoke($event)
    {
        $this->postsRepository->delete(
            basename($event["url"]),
            $event['published']
        );
    }
}
