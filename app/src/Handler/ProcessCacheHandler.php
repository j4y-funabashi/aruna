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
        $eventReader,
        $pipeline
    ) {
        $this->log = $log;
        $this->eventReader = $eventReader;
        $this->pipeline = $pipeline;
    }

    public function handle()
    {
        $initial_id = 0;
        $rpp = 100;

        while (true) {
            $events = $this->eventReader->listFromId($initial_id, $rpp);
            foreach ($events as $event) {
                $this->log->debug("Processing ".$event['uid']);
                try {
                    $event = $this->pipeline->process($event);
                } catch (\Exception $e) {
                    $m = sprintf(
                        "Could not process %s [%s]",
                        $event['uid'],
                        $e->getMessage()
                    );
                    $this->log->critical($m);
                }
            }

            sleep(60);
        }
    }
}
