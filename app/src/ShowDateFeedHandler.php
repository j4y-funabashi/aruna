<?php

namespace Aruna;

use Aruna\Response\Found;

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
        $items = $this->postRepository->listByDate(
            $command->getYear(),
            $command->getMonth(),
            $command->getDay()
        );

        $out = array(
            'items' => $items,
            "title" => sprintf(
                "%s/%s/%s",
                $command->getYear(),
                $command->getMonth(),
                $command->getDay()
            )
        );

        return new Found($out);
    }
}
