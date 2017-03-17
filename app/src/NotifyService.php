<?php

namespace Aruna;

class NotifyService
{
    public function __construct(
        $log
    ) {
        $this->log = $log;
    }

    public function notify($message)
    {
        $this->log->notice($message);
    }
}
