<?php

namespace Aruna\Handler;

/**
 * Class ProcessCacheHandler
 * @author yourname
 */
class ProcessCacheHandler
{
    public function __construct(
        $log,
        $reader,
        $pipeline
    ) {
        $this->log = $log;
        $this->reader = $reader;
        $this->pipeline = $pipeline;
    }

    public function handle()
    {
        $initial_id = 0;
        $rpp = 10;

        $events = $this->reader->listFromId($initial_id, $rpp);

        foreach ($events as $event) {
            $this->log->debug("Processing ".$event['uid']);
            try {
                $this->pipeline->process($event);
            } catch (\Exception $e) {
                $m = sprintf(
                    "Could not process %s [%s]",
                    $event['uid'],
                    $e->getMessage()
                );
                $this->log->critical($m);
            }
        }
    }
}
