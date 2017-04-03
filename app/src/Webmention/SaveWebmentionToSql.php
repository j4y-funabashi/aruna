<?php

namespace Aruna\Webmention;

class SaveWebmentionToSql
{

    public function __construct(
        $log,
        $repository
    ) {
        $this->repository = $repository;
        $this->log = $log;
    }

    public function __invoke($event)
    {
        try {
            $this->repository->save($event);
        } catch (\Exception $e) {
            $this->log->critical(sprintf("Failed to save mention to DB: %s", $e->getMessage()));
        }
        return $event;
    }
}
