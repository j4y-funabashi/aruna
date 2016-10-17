<?php

namespace Aruna;

use Aruna\Response\Found;

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
        $out = array(
            'items' => $this->getItems(),
            'nav' => array(
                0 => array('title' => 'archives', 'items' => $this->postRepository->listMonths())
            )
        );

        return new Found($out);
    }

    private function getItems()
    {
        return array_map(
            function ($post) {
                return new \Aruna\PostViewModel($post, $this->url_generator);
            },
            $this->postRepository->listFromId(0, 10)
        );
    }
}
