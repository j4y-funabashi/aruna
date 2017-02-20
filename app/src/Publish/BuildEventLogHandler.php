<?php

namespace Aruna\Publish;

class BuildEventLogHandler
{
    public function __construct(
        $log,
        $event_store,
        $event_log_repository
    ) {
        $this->log = $log;
        $this->event_store = $event_store;
        $this->event_log_repository = $event_log_repository;
    }

    public function handle()
    {
        foreach ($this->event_store->listAll("posts") as $file) {
            if (isset($file["extension"]) && $file["extension"] == "json") {
                $this->event_log_repository->addEvent(
                    json_decode($this->event_store->readContents($file["path"]), true)
                );
            }
        }
    }
}
