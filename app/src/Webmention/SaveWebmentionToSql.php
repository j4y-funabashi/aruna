<?php

namespace Aruna\Webmention;

use BarnabyWalters\Mf2;

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

        if ($event["mf2"]) {
            $published = Mf2\getPublished(Mf2\findMicroformatsByType($event["mf2"], 'h-entry')[0], true, null);
            if ($published) {
                $event["published"] = $published;
            }
        }

        try {
            $this->repository->save($event);
        } catch (\Exception $e) {
            $this->log->critical(sprintf("Failed to save mention to DB: %s", $e->getMessage()));
        }
        return $event;
    }
}
