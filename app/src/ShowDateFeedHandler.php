<?php

namespace Aruna;

/**
 * Class ShowDateFeedHandler
 * @author yourname
 */
class ShowDateFeedHandler implements Handler
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
            $this->postRepository->listByDate(
                $command->getYear(),
                $command->getMonth(),
                $command->getDay()
            )
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
