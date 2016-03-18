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
        $processPostsPipeline,
        $processMentionsPipeline
    ) {
        $this->log = $log;
        $this->eventStore = $eventStore;
        $this->processPostsPipeline = $processPostsPipeline;
        $this->processMentionsPipeline = $processMentionsPipeline;
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
            try {
                $mention = $this->processMentionsPipeline->process($mention);
            } catch (\Exception $e) {
                $m = sprintf(
                    "Could not process mention %s [%s]",
                    $mention['uid'],
                    $e->getMessage()
                );
                $this->log->critical($m);
            }
        }
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
                    "Could not process post %s [%s]",
                    $event['uid'],
                    $e->getMessage()
                );
                $this->log->critical($m);
            }
        }
    }
}
