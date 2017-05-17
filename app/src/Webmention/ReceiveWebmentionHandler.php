<?php

namespace Aruna\Webmention;

use Aruna\Response\BadRequest;
use Aruna\Response\Accepted;

class ReceiveWebmentionHandler
{
    public function __construct(
        $verifyWebmentionRequest,
        $eventWriter,
        $queue
    ) {
        $this->verify = $verifyWebmentionRequest;
        $this->eventWriter = $eventWriter;
        $this->queue = $queue;
    }

    public function handle($command)
    {
        $mention = array(
            "source" => $command->get("source"),
            "target" => $command->get("target")
        );
        try {
            $this->verify->__invoke($mention);
            $payload = array(
                "mention" => $mention
            );
            $event = new Event($mention);
            $this->eventWriter->save($event);
            $job_id = $this->queue->push(
                'micropub_events',
                json_encode($event)
            );
            return new Accepted($payload);
        } catch (\Exception $e) {
            $payload = array(
                "message" => $e->getMessage(),
                "mention" => $mention
            );
            return new BadRequest($payload);
        }
    }
}
