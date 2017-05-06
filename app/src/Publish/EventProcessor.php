<?php

namespace Aruna\Publish;

class EventProcessor
{
    public function __construct(
        $pipelineFactory
    ) {
        $this->pipelineFactory = $pipelineFactory;
    }

    public function __invoke($event)
    {
        $event_type = $event["type"];
        $pipeline = $this->pipelineFactory->build($event_type);
        $event = $pipeline->process(
            $event["data"]
        );
        return $event;
    }
}
