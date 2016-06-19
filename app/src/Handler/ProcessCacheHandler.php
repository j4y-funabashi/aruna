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
            sleep(60);
        }
    }

    private function processPosts()
    {
        $initial_id = $this->postsRepositoryReader->findLatestId();
        $rpp = 1000;

        $m = sprintf(
            "BEGIN Processing Posts, initial_id: [%s]",
            $initial_id
        );
        $this->log->debug($m);

        $posts = $this->eventStore->listFromId(
            "posts",
            $initial_id,
            $rpp
        );

        if (empty($posts)) {
            $m = sprintf(
                "No new Posts to process",
                $initial_id
            );
            $this->log->debug($m);
        }

        foreach ($posts as $post) {

            $m = sprintf(
                "Processing Post [%s]",
                $post->get("url")
            );
            $this->log->debug($m);

            try {
                $post = $this->processPostsPipeline->process($post);
            } catch (\Exception $e) {
                $m = sprintf(
                    "Could not process post %s [%s]",
                    $post->get("url"),
                    $e->getMessage() . " " . $e->getTraceAsString()
                );
                $this->log->critical($m);
            }
        }
    }
}
