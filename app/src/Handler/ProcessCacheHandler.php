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
        $processMentionsPipeline,
        $postsRepositoryReader,
        $mentionsRepositoryReader
    ) {
        $this->log = $log;
        $this->eventStore = $eventStore;
        $this->processPostsPipeline = $processPostsPipeline;
        $this->processMentionsPipeline = $processMentionsPipeline;
        $this->postsRepositoryReader = $postsRepositoryReader;
        $this->mentionsRepositoryReader = $mentionsRepositoryReader;
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
        $latest = $this->mentionsRepositoryReader->findLatest();
        $initial_id = $latest['uid'];
        $rpp = 100;

        $m = sprintf(
            "BEGIN Processing webmentions, initial_id: [%s]",
            $initial_id
        );
        $this->log->debug($m);

        $mentions = $this->eventStore->listFromId(
            "webmentions",
            $initial_id,
            $rpp
        );

        if (empty($mentions)) {
            $m = sprintf(
                "No new webmentions to process",
                $initial_id
            );
            $this->log->debug($m);
        }

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
        $latest = $this->postsRepositoryReader->findLatest();
        $initial_id = $latest['uid'];
        $rpp = 100;

        $m = sprintf(
            "BEGIN Processing Posts, initial_id: [%s]",
            $initial_id
        );
        $this->log->debug($m);

        $events = $this->eventStore->listFromId(
            "posts",
            $initial_id,
            $rpp
        );

        if (empty($events)) {
            $m = sprintf(
                "No new Posts to process",
                $initial_id
            );
            $this->log->debug($m);
        }

        foreach ($events as $event) {

            $m = sprintf(
                "Processing Post [%s]",
                $event['uid']
            );
            $this->log->debug($m);

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
