<?php

namespace Aruna;

class Queue
{
    private $JOB_PRIORITY = 100;
    private $JOB_DELAY = 0;
    private $JOB_TTR = 120;
    private $QUEUE_TIMEOUT = 3600;

    public function __construct(
        $queue
    ) {
        $this->queue = $queue;
    }

    public function pop($queue_name)
    {
        $job = $this->queue
            ->watch($queue_name)
            ->reserve($this->QUEUE_TIMEOUT);
        return $job;
    }

    public function push(
        $queue_name,
        $job_data
    ) {
        return $this->queue
            ->useTube($queue_name)
            ->put(
                $job_data,
                $this->JOB_PRIORITY,
                $this->JOB_DELAY,
                $this->JOB_TTR
            );
    }

    public function delete($job)
    {
        $this->queue->delete($job);
    }
}
