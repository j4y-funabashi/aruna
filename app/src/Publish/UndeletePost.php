<?php

namespace Aruna\Publish;

class UndeletePost
{
    public function __construct(
        $postsRepository
    ) {
        $this->postsRepository = $postsRepository;
    }

    public function __invoke($event)
    {
        $this->postsRepository->undelete(
            basename($event["properties"]["url"][0])
        );
        return $event;
    }
}
