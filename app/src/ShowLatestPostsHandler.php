<?php

namespace Aruna;

/**
 * Class ShowLatestPostsHandler
 * @author yourname
 */
class ShowLatestPostsHandler implements Handler
{

    public function __construct(
        $postRepository,
        $url_generator
    ) {
        $this->postRepository = $postRepository;
        $this->url_generator = $url_generator;
    }

    public function handle($command)
    {
        $items = array_map(
            function ($post) {
                return new \Aruna\PostViewModel($post, $this->url_generator);
            },
            $this->postRepository->listFromId(0, 10)
        );
        $out = array(
            'items' => $items,
            'nav' => array(
                0 => array('title' => 'archives', 'items' => $this->postRepository->listMonths())
            )
        );

        return new Found($out);
    }
}
