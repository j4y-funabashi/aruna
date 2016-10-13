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
        $postsRepositoryReader,
        $deletePostsPipeline
    ) {
        $this->log = $log;
        $this->eventStore = $eventStore;
        $this->processPostsPipeline = $processPostsPipeline;
        $this->postsRepositoryReader = $postsRepositoryReader;
        $this->deletePostsPipeline = $deletePostsPipeline;
    }

    public function handle()
    {
        $initial_id = $this->postsRepositoryReader->findLatestId();
        $rpp = 10;
        $posts = $this->eventStore->listFromId(
            "posts",
            $initial_id,
            $rpp
        );
        $this->processPosts($posts);
    }

    private function processPosts($posts)
    {
        foreach ($posts as $post) {
            $event_type = $this->getEventType($post);
            $m = sprintf(
                "Processing Event [%s][%s]",
                $event_type,
                $post["uid"]
            );
            $this->log->debug($m);

            if ($event_type == "DeletePost") {
                $post = $this->deletePostsPipeline->process($post);
            }

            if ($event_type == "CreatePost") {
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
    }

    private function getEventType($event)
    {
        if (
            isset($event["action"])
            && $event["action"] == "delete"
        ) {
            return "DeletePost";
        }

        return "CreatePost";
    }
}
