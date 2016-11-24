<?php

namespace Aruna\Micropub;

class UpdatePost
{

    public function __construct($postsRepository)
    {
        $this->postsRepository = $postsRepository;
    }

    public function __invoke($event)
    {
        if (isset($event["properties"]["replace"])) {
            $this->postsRepository->updateReplace(
                basename($event["properties"]["url"][0]),
                $event["properties"]["replace"]
            );
        }
        return $event;
    }
}
