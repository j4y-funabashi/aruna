<?php

namespace Aruna\Webmention;

class ValidateWebmention
{
    public function __construct($log)
    {
        $this->log = $log;
    }

    public function __invoke($event)
    {
        if ($event["error"] != null) {
            return $event;
        }
        try {
            $mention = (new VerifyWebmentionRequest())->__invoke($event);
            $mention = (new VerifyWebmention())->__invoke($mention);
        } catch (\Exception $e) {
            $this->log->error(sprintf("Invalid webmention %s", $e->getMessage()));
            $event["error"] = $e->getMessage();
        }
        return $event;
    }
}
