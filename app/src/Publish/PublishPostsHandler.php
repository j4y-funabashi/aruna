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
        $eventProcessor,
        $queue
    ) {
        $this->log = $log;
        $this->event_log = $event_log;
        $this->queue = $queue;
        $this->eventProcessor = $eventProcessor;
    }

    public function handle()
    {
        $events = $this->event_log->listFromId(1);
        foreach ($events as $event) {
            $this->processEvent($event);
        }

        $QUEUE_EVENTS = 'micropub_events';
        while (true) {
            $m = sprintf("waiting for jobs");
            $this->log->debug($m);

            $job = $this->queue->pop($QUEUE_EVENTS);
            if ($job) {
                $m = sprintf("Got job: [%s] %s", $job->getId(), $job->getData());
                $this->log->debug($m);

                $event = json_decode($job->getData(), true);
                $event = [
                    "id" => $event["eventID"],
                    "type" => $event["eventType"],
                    "data" => $event["eventData"]
                ];
                $this->processEvent($event);
                $this->queue->delete($job);
            }
        }
    }

    private function processEvent($event)
    {
        $event_type = $event["type"];

        $m = sprintf(
            "Processing Event [%s][%s]",
            $event_type,
            $event["id"]
        );
        $this->log->debug($m);

        try {
            $event = $this->eventProcessor->__invoke(
                $event
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
