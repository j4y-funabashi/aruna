<?php

namespace Aruna\WebMention;

use Aruna\EventWriter;
use Aruna\EventReader;
use Aruna\Event;

/**
 * Class WebMentionHandler
 * @author yourname
 */
class WebMentionHandler
{
    public function __construct(
        EventWriter $eventWriter,
        EventReader $eventReader
    ) {
        $this->eventWriter = $eventWriter;
        $this->eventReader = $eventReader;
    }

    public function recieve(array $mention)
    {
        if (
            false === isset($mention['target'])
            || false === isset($mention['source'])
        ) {
            throw new \InvalidArgumentException();
        }

        $event = new Event($mention);
        $this->eventWriter->save($event);
        return $event->getUid();
    }

    public function findById($mention_id)
    {
        $event = $this->eventReader->findById($mention_id);
        return $event[0];
    }
}
