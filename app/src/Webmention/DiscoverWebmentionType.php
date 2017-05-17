<?php

namespace Aruna\Webmention;

class DiscoverWebmentionType
{

    public function __invoke($event)
    {
        if ($event["error"]) {
            $event["type"] = "error";
            return $event;
        }
        $event["type"] = $this->discoverType($event["mf2"]["items"][0]);
        return $event;
    }

    private function discoverType($mf2)
    {
        if (isset($mf2["properties"]["in-reply-to"])) {
            return "comment";
        }
        if (isset($mf2["properties"]["like-of"])) {
            return "like";
        }
        return "mention";
    }
}
