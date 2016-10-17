<?php

namespace Aruna;

use Aruna\Response\BadRequest;
use Aruna\Response\Accepted;

class ReceiveWebmentionHandler implements Handler
{
    public function __construct(
        $verifyWebmentionRequest,
        $eventWriter
    ) {
        $this->verify = $verifyWebmentionRequest;
        $this->eventWriter = $eventWriter;
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
