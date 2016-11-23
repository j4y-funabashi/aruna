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
            basename($event["properties"]["url"][0]),
            $event["properties"]['published'][0]
        );
        return $event;
    }
}
