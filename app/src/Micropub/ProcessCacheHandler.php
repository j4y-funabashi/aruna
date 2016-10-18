<?php

namespace Aruna\Micropub;

/**
 * Class ProcessCacheHandler
 * @author yourname
 */
class ProcessCacheHandler
{
    public function __construct(
        $log,
        $eventStore,
        $postsRepositoryReader,
        $pipelineFactory
    ) {
        $this->log = $log;
        $this->eventStore = $eventStore;
        $this->postsRepositoryReader = $postsRepositoryReader;
        $this->pipelineFactory = $pipelineFactory;
    }

    public function handle()
    {
        $initial_id = $this->postsRepositoryReader->findLatestId();
        $rpp = 1000;
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
            $pipeline = $this->pipelineFactory->build($event_type);
            $m = sprintf(
                "Processing Event [%s][%s]",
                $event_type,
                $post["uid"]
            );
            $this->log->debug($m);

            try {
                $post = $pipeline->process($post);
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
        if (
            isset($event["action"])
            && $event["action"] == "delete"
        ) {
            return "DeletePost";
        }

        return "CreatePost";
    }
}
