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
        $mentionsReader,
        $pipeline
    ) {
        $this->log = $log;
        $this->eventReader = $eventReader;
        $this->mentionsReader = $mentionsReader;
        $this->pipeline = $pipeline;
    }

    public function handle()
    {

        //while (true) {
            $this->processEvents();
            $this->processMentions();
            //sleep(60);
        //}
    }

    private function processMentions()
    {
        $initial_id = 0;
        $rpp = 100;

        $mentions = $this->mentionsReader->listFromId($initial_id, $rpp);

        foreach ($mentions as $mention) {

            if (false !== stripos($mention['target'], "http://j4y.co/")) {

                $http = new \GuzzleHttp\Client();
                $result = $http->get(
                    $mention['source']
                );
                $source_body = trim($result->getBody());
                if (false !== stripos($source_body, $mention['target'])) {
                    //$mf = \Mf2\fetch($mention['source']);
                    var_dump($source_body);
                }
            }
        }
    }

    private function processEvents()
    {
        $initial_id = 0;
        $rpp = 100;

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
    }
}
