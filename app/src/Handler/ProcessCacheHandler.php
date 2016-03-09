<?php

namespace Aruna\Handler;

/**
 * Class ProcessCacheHandler
 * @author yourname
 */
class ProcessCacheHandler
{
    public function __construct(
        $log
    ) {
        $this->log = $log;
    }

    public function handle()
    {
        $this->log->info("hello");
    }
}
