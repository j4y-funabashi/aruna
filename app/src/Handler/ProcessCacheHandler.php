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
        $postsRepositoryReader
    ) {
        $this->log = $log;
        $this->eventStore = $eventStore;
        $this->processPostsPipeline = $processPostsPipeline;
        $this->postsRepositoryReader = $postsRepositoryReader;
    }

    public function handle()
    {
        $this->processPosts();
    }

    private function processPosts()
    {
        $initial_id = $this->postsRepositoryReader->findLatestId();
        $rpp = 10;

        $posts = $this->eventStore->listFromId(
            "posts",
            $initial_id,
            $rpp
        );


        foreach ($posts as $post) {
            $event_type = $this->getEventType($post);
            $m = sprintf(
                "Processing Event [%s][%s]",
                $event_type,
                $post["uid"]
            );
            $this->log->debug($m);

            try {
                $post = $this->processPostsPipeline->process($post);
            } catch (\Exception $e) {
                $m = sprintf(
                    "Could not process post %s [%s]",
                    $post["uid"],
                    $e->getMessage() . " " . $e->getTraceAsString()
                );
                $this->log->critical($m);
            }
        }
    }

    private function getEventType($event)
    {
        return "CreatePost";
    }
}
