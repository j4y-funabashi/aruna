<?php

namespace Aruna\Reader;

use Aruna\Response\Found;
use Aruna\Handler;

class ShowTaggedHandler implements Handler
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
        $tag = $command->get("tag");

        $payload = array(
            "feed_title" => $tag,
            "items" => $this->getPostsTagged($tag, $rpp, $offset),
            "nav_next" => $this->url_generator->generate(
                "tagged",
                array("tag" => $tag, "page" => $next_page)
            )
        );

        return new Found($payload);
    }

    private function getPostsTagged($tag, $rpp, $offset)
    {
        return $this->postsRepository->listByTag($tag, $rpp, $offset);
    }
}
