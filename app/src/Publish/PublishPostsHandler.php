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
        $event_log,
        $postsRepositoryReader,
        $pipelineFactory
    ) {
        $this->log = $log;
        $this->event_log = $event_log;
        $this->postsRepositoryReader = $postsRepositoryReader;
        $this->pipelineFactory = $pipelineFactory;
    }

    public function handle()
    {
        $events = $this->event_log->listFromId(1);
        foreach ($events as $event) {
            $this->processEvent($event);
        }
    }

    private function processEvent($event)
    {
        $event_type = $event["type"];
        $pipeline = $this->pipelineFactory->build($event_type);

        $m = sprintf(
            "Processing Event [%s][%s]",
            $event_type,
            $event["id"]
        );
        $this->log->debug($m);

        try {
            $event = $pipeline->process(
                json_decode($event["data"], true)
            );
        } catch (\Exception $e) {
            $m = sprintf(
                "Could not process post %s [%s]",
                $event["id"],
                $e->getMessage() . " " . $e->getTraceAsString()
            );
            $this->log->critical($m);
        }
    }
}
