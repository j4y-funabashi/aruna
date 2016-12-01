<?php

namespace Aruna\Publish;

/**
 * Class PublishPostsHandler
 * @author yourname
 */
class PublishPostsHandler
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

    public function handle($rpp)
    {
        $posts = $this->eventStore->listFromId(
            "posts",
            $this->postsRepositoryReader->findLatestId(),
            $rpp
        );
        $this->processPosts($posts);
    }

    private function processPosts($posts)
    {
        foreach ($posts as $post) {
            $event_type = $post["eventType"];
            $pipeline = $this->pipelineFactory->build($event_type);
            $m = sprintf(
                "Processing Event [%s][%s]",
                $event_type,
                $post["eventID"]
            );
            $this->log->debug($m);
            try {
                $post = $pipeline->process($post["eventData"]);
            } catch (\Exception $e) {
                $m = sprintf(
                    "Could not process post %s [%s]",
                    $post["eventID"],
                    $e->getMessage() . " " . $e->getTraceAsString()
                );
                $this->log->critical($m);
            }
        }
    }

    private function getEventType($event)
    {
        return $event["eventType"];
    }
}
