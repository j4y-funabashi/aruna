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
        $eventStore,
        $processPostsPipeline
    ) {
        $this->log = $log;
        $this->eventStore = $eventStore;
        $this->processPostsPipeline = $processPostsPipeline;
    }

    public function handle()
    {
        while (true) {
            $this->processPosts();
            $this->processMentions();
            sleep(60);
        }
    }

    private function processMentions()
    {
        $initial_id = 0;
        $rpp = 100;

        $mentions = $this->eventStore->listFromId(
            "webmentions",
            $initial_id,
            $rpp
        );
        foreach ($mentions as $mention) {
            $out_file = "processed_webmentions/".$mention['uid'].".json";
            if ($this->eventStore->exists($out_file)) {
                continue;
            }

            if (false !== stripos($mention['target'], "http://j4y.co")) {
                $mention['target_uid'] = $this->getTargetUid($mention['target']);
                $http = new \GuzzleHttp\Client();
                $result = $http->get(
                    $mention['source']
                );
                $source_body = trim($result->getBody());
                if (false !== stripos($source_body, $mention['target'])) {
                    $mention['source_mf2_json'] = \Mf2\parse($source_body);
                    $this->eventStore->save(
                        $out_file,
                        json_encode($mention)
                    );
                }
            }
        }
    }

    private function getTargetUid($target_url)
    {
        return basename(parse_url($target_url, PHP_URL_PATH));
    }

    private function processPosts()
    {
        $initial_id = 0;
        $rpp = 100;

        $events = $this->eventStore->listFromId(
            "posts",
            $initial_id,
            $rpp
        );
        foreach ($events as $event) {
            try {
                $event = $this->processPostsPipeline->process($event);
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
