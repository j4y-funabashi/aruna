<?php

namespace Aruna\Webmention;

use RuntimeException;

/**
 * Class EventWriter
 * @author yourname
 */
class EventWriter
{
    public function __construct(
        $filesystem
    ) {
        $this->filesystem = $filesystem;
    }

    public function save(
        Event $event
    ) {

        try {
            $this->filesystem->write(
                $event->getUid().".json",
                json_encode($event)
            );
        } catch (\Exception $e) {
            throw new RuntimeException($e->getMessage());
        }
    }
}
