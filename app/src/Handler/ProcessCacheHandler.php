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
        $mentionsWriter,
        $pipeline,
        $processed_mentions_root
    ) {
        $this->log = $log;
        $this->eventReader = $eventReader;
        $this->mentionsReader = $mentionsReader;
        $this->mentionsWriter = $mentionsWriter;
        $this->pipeline = $pipeline;
        $this->processed_mentions_root = $processed_mentions_root;
    }

    public function handle()
    {
        while (true) {
            $this->processEvents();
            $this->processMentions();
            sleep(60);
        }
    }

    private function processMentions()
    {
        $initial_id = 0;
        $rpp = 100;

        $mentions = $this->mentionsReader->listFromId($initial_id, $rpp);
        foreach ($mentions as $mention) {
            if (file_exists($this->processed_mentions_root."/".$mention['uid'].".json")) {
                continue;
            }

            if (
                false !== stripos($mention['target'], "http://j4y.co")
            ) {
                $mention['target_uid'] = $this->getTargetUid($mention['target']);
                $http = new \GuzzleHttp\Client();
                $result = $http->get(
                    $mention['source']
                );
                $source_body = trim($result->getBody());
                if (false !== stripos($source_body, $mention['target'])) {
                    $mention['source_mf2_json'] = \Mf2\parse($source_body);
                    $this->mentionsWriter->save($mention);
                }
            }
        }
    }

    private function getTargetUid($target_url)
    {
        return basename(parse_url($target_url, PHP_URL_PATH));
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
