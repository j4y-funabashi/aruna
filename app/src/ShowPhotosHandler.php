<?php

namespace Aruna;

use Aruna\Response\Found;

class ShowPhotosHandler implements Handler
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
        $page = ($command->get("page"))
            ? $command->get("page")
            : 0;
        $rpp = $command->get("rpp");
        $offset = ($page == 0)
            ? 0
            : ($page - 1) * $rpp;
        $next_page = ($page > 1)
            ? $page + 1
            : 2;

        $payload = array(
            "items" => $this->getPhotos($rpp, $offset),
            "nav_next" => $this->url_generator->generate(
                "photos",
                array("page" => $next_page)
            )
        );

        return new Found($payload);
    }

    private function getPhotos($rpp, $offset)
    {
        return $this->postsRepository->listByType("photo", $rpp, $offset);
    }
}
