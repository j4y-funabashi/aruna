<?php

namespace Aruna;

class ShowPhotosHandler
{
    public function __construct(
        $postRepository,
        $url_generator
    ) {
        $this->postsRepository = $postRepository;
        $this->url_generator = $url_generator;
    }

    public function getLatestPhotos($rpp, $page = 1)
    {
        $offset = ($page - 1) * $rpp;
        $next_page = ($page > 1)
            ? $page + 1
            : 2;
        return new Found(
            array(
                "items" => array_map(
                    function ($post) {
                        return new \Aruna\PostViewModel(
                            $post,
                            $this->url_generator
                        );
                    },
                    $this->postsRepository
                    ->listByType("photo", $rpp, $offset)
                ),
                "nav_next" => $this->url_generator->generate(
                    "photos",
                    array("page" => $next_page)
                )
            )
        );
    }
}
