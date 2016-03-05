<?php

namespace Aruna\WebMention;

use Aruna\EventWriter;

/**
 * Class WebMentionHandler
 * @author yourname
 */
class WebMentionHandler
{
    public function __construct(
        EventWriter $eventWriter
    ) {
        $this->eventWriter = $eventWriter;
    }

    public function recieve(array $mention)
    {
        if (
            false === isset($mention['target'])
            || false === isset($mention['source'])
        ) {
            throw new \InvalidArgumentException();
        }

        $this->eventWriter->save($mention);
    }
}
