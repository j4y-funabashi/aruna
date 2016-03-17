<?php

namespace Aruna\Action;

/**
 * Class ResizePhoto
 * @author yourname
 */
class ResizePhoto
{
    public function __construct(
        $log,
        $resizer
    ) {
        $this->log = $log;
        $this->resizer = $resizer;
    }

    public function __invoke($event)
    {
        if (isset($event['files']['photo'])) {
            $m = sprintf(
                "Resizing photo [%s]",
                $event['files']['photo']
            );
            $this->resizer->resize($event['files']['photo']);
        }

        return $event;
    }
}
